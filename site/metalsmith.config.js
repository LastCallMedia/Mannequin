
import Metalsmith from 'metalsmith';
import markdown from 'metalsmith-markdown';
import webpack from 'metalsmith-webpack-2';
import sass from 'metalsmith-sass';
import ignore from 'metalsmith-ignore';
import collections from 'metalsmith-collections';
import twig from 'metalsmith-twig';
import headings from 'metalsmith-headings';
import postcss from 'metalsmith-postcss';
import cheerio from 'cheerio';
import match from 'multimatch';
import path from 'path';
import tildeImporter from 'node-sass-tilde-importer';

let metalsmith = Metalsmith(__dirname);

metalsmith.ignore([ '**.twig']); // Ignore all Twig templates.
metalsmith.source('./src');
metalsmith.destination('./dist');
metalsmith.metadata({
    google_analytics: process.env.GOOGLE_ANALYTICS || null,
    cache_buster: Math.floor(Date.now() / 1000)
});

// Add a default layout to all markdown files.
metalsmith.use((files, metalsmith, done) => {
    Object.keys(files).forEach(file => {
        if(file.match(/\.md$/) && !files[file].view) {
            files[file].view = 'default.twig';
        }
        files[file].originalPath = files[file].path || file;
    });
    done();
});
metalsmith.use(webpack('webpack.config.js', ['js/**.es6.js']));
metalsmith.use(ignore(['js/main.es6.js']));
metalsmith.use(sass({
    outputDir: 'css/',
    importer: tildeImporter,
    sourceMap: true,
    sourceMapContents: true
}));
metalsmith.use(postcss({
    plugins: { 'autoprefixer': {} },
    map: {inline: false}
}));
metalsmith.use(markdown({gfm: true}));
// Converts .md references in links to .html ones.
// This allows us to write GH compatible markdown files that will resolve properly on the site as well.
// @todo: Move into external file.
metalsmith.use((files, metalsmith, done) => {
    const root = metalsmith.directory();
    // Build a map of original filenames (.md) to current filenames (.html).
    const map = Object.keys(files).reduce((m, file) => {
        m[files[file].originalPath] = files[file].path || file;
        return m;
    }, {});
    match(Object.keys(files), '**/*.html').forEach(file => {
        var dir = path.dirname(file);
        var $ = cheerio.load(files[file].contents.toString());
        $('a[href]').each((i, el) => {
            const href = $(el).attr('href');
            if(!href.match(/https?\:\/\//)) {
                var parts = href.split('#');
                // rootRel is the relative path for the link from the root of the metalsmith installation.
                const rootRel = parts[0][0] === '/'
                    ? parts[0].slice(1)
                    : path.normalize(path.relative(root, path.resolve(dir, parts[0])));
                if(map.hasOwnProperty(rootRel)) {
                    var fileRel = path.relative(path.dirname(file), map[rootRel]);
                    $(el).attr('href', `${fileRel}${parts[1] ? '#' + parts[1] : ''}`);
                }
            }
        });
        files[file].contents = new Buffer($.html());
    })
    done();
});
metalsmith.use(headings({selectors: ['h2', 'h3']}));
metalsmith.use(collections());
metalsmith.use(twig({
    directory: './src/layouts',
    pattern: ['**/*.html'],
}));

module.exports = metalsmith;