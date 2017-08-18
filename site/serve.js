
var browserSync = require('browser-sync');

browserSync.create()
    .init({
        open: true,
        server: {
            baseDir: 'build',
            index: 'index'
        }
    })