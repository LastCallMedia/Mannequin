
var path = require('path');

module.exports = {
    entry: {
        app: [path.resolve('src', 'js', 'main.js')]
    },
    output: {
        path: path.resolve(__dirname + 'dist', 'js'),
        filename: '[name].[chunkhash].js'
    }
}