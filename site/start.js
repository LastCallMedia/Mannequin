
import browserSync from 'browser-sync';
import metalsmith from './metalsmith.config';
import nodemon from 'nodemon';

// Start browsersync:
const bs = browserSync.create();
bs.init({
    open: true,
    server: {
        baseDir: metalsmith._destination,
        index: 'index.html'
    }
});

// Start nodemon to watch the files, then trigger the build script.
const np = nodemon({
    exec: 'npm run build',
    ext: "js json md scss html png svg jpg",
    restartable: 'rs',
    ignore: [
        ".git",
        "node_modules/**/node_modules",
        "cache",
        "dist"
    ],
    watch: [
        "src",
        "metalsmith.js",
        "build.js"
    ],
});

// Refresh the browser when a build is run.
np.on('exit', () => {
    bs.reload('*');
});
// Fix for Error: read EIO.  See https://github.com/remy/nodemon/pull/976
np.on('quit', process.exit);


