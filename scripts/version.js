
var fs = require('fs');

function updateVersionFile(tag) {
    const re = new RegExp(`const TAG = '[\d\.a-z0-9]+'`);
    return (filename) => new Promise((res, rej) => {
        fs.readFile(filename, 'utf-8', (err, contents) => {
            if(err) {
                rej(err);
                return;
            }
            fs.writeFile(filename, contents.replace(re, `const TAG = '${tag}'`));
            res();
        })
    });
}

class AppVersionRelease {
    constructor(tag, packages) {
        this.tag = tag;
        this.packages = packages;
    }
    files() {
        return this.packages.filter(p => !!p.versionClass).map(p => p.versionClass);
    }
    preflight() {
        return Promise.resolve();
    }
    modify() {
        return Promise.all(this.files().map(updateVersionFile(this.tag)));
    }
    commit() {
        return Promise.resolve();
    }
}

module.exports = AppVersionRelease;

