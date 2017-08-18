
var Metalsmith = require('metalsmith');
var markdown = require('metalsmith-markdown');
var webpack = require('metalsmith-webpack-2');
var webpackConfig = require('./webpack.config');
var path = require('path');

let metalsmith = Metalsmith(__dirname);

metalsmith.source(path.resolve(__dirname, 'src', 'content'));
metalsmith.destination(path.resolve(__dirname, 'dist'));

metalsmith.use(markdown({gfm: true}));
metalsmith.use(webpack.default(webpackConfig));



module.exports = metalsmith;