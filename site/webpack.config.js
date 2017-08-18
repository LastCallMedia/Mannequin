
var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

const extractSass = new ExtractTextPlugin({
    filename: '[name].[contenthash].css',
});

module.exports = [
    {
        entry: {
            app: [path.resolve('src', 'js', 'main.js')]
        },
        output: {
            path: path.resolve(__dirname, 'dist', 'js'),
            filename: '[name].[chunkhash].js'
        },
        module: {
            rules: [{
                test: /\.js$/,
                include: [],
                exclude: [/node_modules/],
                loader: 'babel-loader'
            }]
        }
    },
    {
        entry: {
            style: [path.resolve('src', 'scss', 'main.scss')]
        },
        output: {
            path: path.resolve(__dirname, 'dist', 'css'),
            filename: '[name].[chunkhash].css'
        },
        module: {
            rules: [{
                test: /\.scss$/,
                use: extractSass.extract({
                    use: [
                        {loader: "css-loader"},
                        {loader: "sass-loader"},
                    ],
                    fallback: 'style-loader'
                }),
            }]
        },
        plugins: [extractSass]
    }
];