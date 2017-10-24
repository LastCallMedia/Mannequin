
var read = require('load-json-file');
var write = require('write-json-file');

function modifyFile(file, tag, branch) {
    return read(file)
        .then(contents => {
            const [major, minor] = tag.split('.');
            Object.keys(contents.require)
                .filter(r => r.indexOf('lastcall/mannequin') === 0)
                .forEach(r => {
                    contents.require[r] = `~${major}.${minor}`
                })
            if(contents.extra) {
                if(contents.extra['branch-alias']) {
                    contents.extra['branch-alias']['dev-' + branch] = `${major}.${minor}-dev`;
                }
                if(contents.extra['extra-files']) {
                    contents.extra['extra-files'].ui.url = `https://registry.npmjs.org/lastcall-mannequin-ui/-/lastcall-mannequin-ui-${tag}.tgz`
                }
            }
            return Promise.resolve(contents);
        })
        .then(contents => {
            return write(file, contents, {indent: 4});
        });
}

function hasComposer(pack) {
    return !!pack.composer;
}


class ComposerRelease {
    constructor(tag, branch, packages) {
        this.tag = tag;
        this.branch = branch;
        this.packages = packages;
    }
    files() {
        return this.packages.filter(hasComposer).map(pack => pack.composer);
    }
    preflight() {
        return Promise.resolve();
    }
    modify() {
        return Promise.all(this.packages.filter(hasComposer).map(p => {
            return modifyFile(p.composer, this.tag, this.branch);
        }))
    }
    commit() {
        return Promise.resolve();
    }
}

module.exports = ComposerRelease;
