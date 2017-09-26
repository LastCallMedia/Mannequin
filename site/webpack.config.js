
var path = require('path');
const webpack = require('webpack');

module.exports = {
    bail: true,
    entry: {
        app: path.resolve('src', 'js', 'main.es6.js')
    },
    output: {
        path: path.resolve(__dirname, 'dist', 'js'),
        filename: '[name].[chunkhash].js',
    },
    plugins: [
        new webpack.optimize.UglifyJsPlugin()
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules\/(?!(foundation-sites)\/).*/,
                loader: 'babel-loader',
            },
        ]
    }
};