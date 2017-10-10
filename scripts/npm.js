
var read = require('load-json-file');
var write = require('write-json-file');

function bumpNpmVersion(tag, file) {
    return read(file)
        .then(contents => {
            contents.version = tag;
            return contents;
        })
        .then(contents => {
            return write(file, contents, {indent: 2});
        });
}
function hasNpm(pack) {
    return !!pack.npm;
}

class NpmRelease {
    constructor(tag, packages) {
        this.tag = tag;
        this.packages = packages;
    }
    files() {
        return this.packages.filter(hasNpm).map(pack => pack.npm);
    }
    preflight() {
        return Promise.all(this.packages.filter(hasNpm).map(pack => {
            return read(pack.npm);
        }));
    }
    modify() {
        return Promise.all(this.packages.filter(hasNpm).map(pack => {
            return bumpNpmVersion(this.tag, pack.npm)
        }));
    }
    commit() {
        return Promise.resolve();
    }
}

module.exports = NpmRelease;