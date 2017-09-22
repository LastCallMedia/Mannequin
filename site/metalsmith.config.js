
import Metalsmith from 'metalsmith';
import markdown from 'metalsmith-markdown';
import webpack from 'metalsmith-webpack-2';
import ignore from 'metalsmith-ignore';
import collections from 'metalsmith-collections';
import twig from 'metalsmith-twig';
import permalinks from 'metalsmith-permalinks';
import headings from 'metalsmith-headings';

let metalsmith = Metalsmith(__dirname);


metalsmith.source('./src');
metalsmith.destination('./dist');
metalsmith.ignore(['scss', 'layouts']);
metalsmith.use(ignore(['scss/*', 'js/*', 'layouts/*']));
// Add a default layout to all markdown files.
metalsmith.use((files, metalsmith, done) => {
    Object.keys(files).forEach(file => {
        if(file.match(/\.md$/)) {
            if(!files[file].view) {
                files[file].view = 'default.twig';
            }
        } else {
            // files[file].permalink = false;
        }
    });
    done();
})
metalsmith.use(markdown({gfm: true}));
metalsmith.use(headings());
metalsmith.use(collections());
metalsmith.use(permalinks({
    relative: false
}));
metalsmith.use(webpack('webpack.config.js', ['./src/js/**', './src/scss/**']));
metalsmith.use(twig({
    directory: './src/layouts',
    pattern: ['**/*.html'],
}));




module.exports = metalsmith;