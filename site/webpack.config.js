
var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

const extractSass = new ExtractTextPlugin({
    filename: '../css/[name].[contenthash].css',
});

module.exports = {
    bail: true,
    entry: {
        app: path.resolve('src', 'js', 'main.js'),
        style: path.resolve('src', 'scss', 'main.scss')
    },
    output: {
        path: path.resolve(__dirname, 'dist', 'js'),
        filename: '[name].[chunkhash].js',
    },
    plugins: [extractSass],
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [],
                exclude: [/node_modules/],
                loader: 'babel-loader'
            },
            {
                test: /\.scss$/,
                use: extractSass.extract({
                    use: [
                        {loader: 'css-loader'},
                        {loader: 'postcss-loader'},
                        {loader: 'sass-loader'}
                    ],
                    fallback: 'style-loader'
                })
            },
            {
                test: [/\.bmp$/, /\.gif$/, /\.jpe?g$/, /\.png$/],
                loader: 'url-loader',
                options: {
                    name: '../img/[name].[hash:8].[ext]'
                }
            }
        ]
    }
};