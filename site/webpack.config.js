
var path = require('path');
const webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

const extractSass = new ExtractTextPlugin({
    filename: '../css/[name].[contenthash].css',
});

module.exports = {
    bail: true,
    entry: {
        app: path.resolve('src', 'js', 'main.es6.js'),
        style: path.resolve('src', 'scss', 'main.scss')
    },
    output: {
        path: path.resolve(__dirname, 'dist', 'js'),
        filename: '[name].[chunkhash].js',
    },
    plugins: [
        extractSass,
        new webpack.optimize.UglifyJsPlugin()
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules\/(?!(foundation-sites)\/).*/,
                loader: 'babel-loader',
            },
            {
                test: /\.scss$/,
                use: extractSass.extract({
                    use: [
                        {loader: 'css-loader'},
                        {loader: 'postcss-loader'},
                        {loader: 'sass-loader', options: {
                            outputStyle: 'compressed'
                        }}
                    ],
                    fallback: 'style-loader'
                })
            },
            {
                test: [/\.bmp$/, /\.gif$/, /\.jpe?g$/, /\.png$/, /\.svg$/, /\.(ttf|eot|woff|woff2)$/],
                loader: 'url-loader',
                options: {
                    limit: 10000,
                    name: '../img/[name].[hash:8].[ext]',
                    fallback: 'file-loader'
                }
            }
        ]
    }
};