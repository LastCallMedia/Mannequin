
import Metalsmith from 'metalsmith';
import markdown from 'metalsmith-markdown';
import webpack from 'metalsmith-webpack-2';
import ignore from 'metalsmith-ignore';
import collections from 'metalsmith-collections';
import twig from 'metalsmith-twig';

var path = require('path');

let metalsmith = Metalsmith(__dirname);


metalsmith.source('./src');
metalsmith.destination('./dist');
metalsmith.ignore(['scss', 'layouts']);
metalsmith.use(ignore(['scss/*', 'js/*', 'layouts/*']));
// Add a default layout to all markdown files.
metalsmith.use((files, metalsmith, done) => {
    Object.keys(files).forEach(file => {
        if(file.match(/\.md$/) && !files[file].view) {
            files[file].view = 'default.twig';
        }
    });
    done();
})
metalsmith.use(markdown({gfm: true}));
metalsmith.use(collections({
    Extensions: {pattern: 'extensions/**.md'}
}));

metalsmith.use(webpack('webpack.config.js', ['./src/js/**', './src/scss/**']));
metalsmith.use(twig({
    directory: './src/layouts',
    pattern: ['**/*.html'],
}));




module.exports = metalsmith;