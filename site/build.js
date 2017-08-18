

var http = require('http');
var metalsmith = require('./metalsmith');

metalsmith.build((err, files) => {
    if(err) throw err;
    http.get('http://localhost:3000/__browser_sync__?method=reload')
        .on('error', () => console.log('browserSync not listening?'))
    console.log('finished');
})