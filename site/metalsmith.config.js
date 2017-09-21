
import Metalsmith from 'metalsmith';
import markdown from 'metalsmith-markdown';
import webpack from 'metalsmith-webpack-2';
import layouts from 'metalsmith-layouts';
import ignore from 'metalsmith-ignore';
import collections from 'metalsmith-collections';

var path = require('path');
var handlebars = require('handlebars');

let metalsmith = Metalsmith(__dirname);


metalsmith.source('./src');
metalsmith.destination('./dist');
metalsmith.ignore(['scss', 'layouts']);
metalsmith.use(ignore(['scss/*', 'js/*', 'layouts/*']));
metalsmith.use(markdown({gfm: true}));
metalsmith.use(collections({
    Extensions: {pattern: 'extensions/**.md'}
}));

metalsmith.use(webpack('webpack.config.js', ['./src/js/**', './src/scss/**']));
metalsmith.use(layouts({
    engine: 'handlebars',
    directory: './src/layouts',
    default: 'default.html',
    partials: './src/layouts/partials',
    pattern: ['**/*.html'],
    exposeConsolidate: (consolidateRequires) => {
        consolidateRequires.handlebars = handlebars;
        var helpers = require('handlebars-helpers')({
            handlebars: handlebars
        });
    }
}));
metalsmith.clean(false);




module.exports = metalsmith;