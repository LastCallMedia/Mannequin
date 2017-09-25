
import Metalsmith from 'metalsmith';
import markdown from 'metalsmith-markdown';
import webpack from 'metalsmith-webpack-2';
import ignore from 'metalsmith-ignore';
import collections from 'metalsmith-collections';
import twig from 'metalsmith-twig';
import permalinks from 'metalsmith-permalinks';
import headings from 'metalsmith-headings';

let metalsmith = Metalsmith(__dirname);

metalsmith.ignore(['layouts']); // Ignore entire layouts directory.
metalsmith.source('./src');
metalsmith.destination('./dist');

metalsmith.use(webpack('webpack.config.js', ['js/**/*.es6.js', 'scss/**']));
metalsmith.use(ignore(['**/*.scss', '**/*.es6.js'])); // Remove webpack files so they don't end up in dist.
// Add a default layout to all markdown files.
metalsmith.use((files, metalsmith, done) => {
    Object.keys(files).forEach(file => {
        if(file.match(/\.md$/)) {
            if(!files[file].view) {
                files[file].view = 'default.twig';
            }
        }
    });
    done();
})
metalsmith.use(markdown({gfm: true}));
metalsmith.use(headings({selectors: ['h2', 'h3']}));
metalsmith.use(collections());
metalsmith.use(permalinks({
    relative: false
}));
metalsmith.use(twig({
    directory: './src/layouts',
    pattern: ['**/*.html'],
}));




module.exports = metalsmith;