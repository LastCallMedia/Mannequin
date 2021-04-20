
var exec = require('child-process-promise').exec;

function tagNotExists(tag) {
    return exec(`git rev-parse -q -v ${tag}^{tree}`)
        .then(res => { throw new Error('Tag already exists')})
        .catch(err => {
           if(err.code === 128) {
               return;
           }
           throw err;
        });
}

function filesClean(files) {
    return exec(`git status --porcelain ${files.join(' ')}`)
        .then(result => {
            if(result.stdout.length > 0) {
                throw new Error('Files not clean');
            }
        });
}

function onBranch(branch) {
    return exec('git rev-parse --abbrev-ref HEAD')
        .then(result => {
            if(result.stdout.trim() !== branch) {
                throw new Error(`Must be on ${branch} branch`)
            }
        })
}

function upToDate(origin, branch) {
    return exec(`git fetch ${origin} ${branch}`)
        .then(() => exec(`git log ..${origin}/${branch}`))
        .then(result => {
            if(result.stdout.trim().length > 0) {
                throw new Error(`Not up to date with ${origin}/${branch}`);
            }
        })
}

function commitAndTag(files, tag, message) {
    return exec(`git add ${files.join(' ')}`)
        .then(() => exec(`git commit -m "${message}"`))
        .then(() => exec(`git tag ${tag}`));
}

class GitRelease {
    constructor(tag, origin, branch, message) {
        this.tag = tag;
        this.origin = origin;
        this.branch = branch;
        this.message = message;
    }
    files() {
        return [];
    }
    preflight(files) {
        return Promise.all([
            tagNotExists(this.tag),
            onBranch(this.branch),
            upToDate(this.origin, this.branch),
            filesClean(files)
        ])
        return Promise.resolve();
    }
    modify() {
        return Promise.resolve();
    }
    commit(files) {
        // Add, commit and tag.
        return commitAndTag(files, this.tag, this.message);
    }
}

module.exports = GitRelease;
