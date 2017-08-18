
var metalsmith = require('./metalsmith.config');

metalsmith.build((err, files) => {
    if(err) throw err;
});