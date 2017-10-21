
var fs = require('fs');
var GitRelease = require('./git');
var ComposerRelease = require('./composer');
var ChangelogRelease = require('./changelog.js');
var NpmRelease = require('./npm');
var VersionRelease = require('./version');
var args = require('minimist')(process.argv.slice(2), {
    string: ['branch'],
    default: {
        commit: true,
        branch: 'master'
    }
});

var globby = require('globby');
var path = require('path');

const tag = args._[0];
const branch = args.branch;
const message = args.message || `Tagging for ${tag} release`;
const gitRoot = path.resolve(__dirname, '..');
const commit = args.commit;

if(!tag || !tag.match(/(v?)\d+\.\d+\.\d+(-(alpha|beta|rc)\d+)?/)) {
    throw new Error(`Invalid tag string: ${tag}`);
}
if(!branch || branch.length === 0) {
    throw new Error(`Invalid branch.`)
}
if(!message || message.length === 0) {
    throw new Error('Invalid message.');
}

var packages = [
    {name: 'Demo', changelog: path.join(__dirname, '../demo/CHANGELOG.md'), composer: path.join(__dirname, '../demo/composer.json')},
    {name: 'Site', changelog: path.join(__dirname, '../site/CHANGELOG.md'), npm: path.join(__dirname, '../site/package.json')},
    {name: 'Ui', changelog: path.join(__dirname, '../ui/CHANGELOG.md'), npm: path.join(__dirname, '../ui/package.json')},
];

fileIfExists = (filename) => {
    try {
        fs.statSync(filename)
        return filename
    }
    catch(err) {
        return;
    }
}
// Add composer packages.
globby.sync([path.join(__dirname, '../src/*')]).map(dir => {
    packages.push({
        name: path.basename(dir),
        composer: path.join(dir, 'composer.json'),
        changelog: path.join(dir, 'CHANGELOG.md'),
        versionClass: fileIfExists(path.join(dir, 'Version.php')),
    })
});

class Release {
    constructor(releasers, root) {
        this.releasers = releasers;
        this.root = root;
    }
    files() {
        return [].concat(...this.releasers.map(r => {
            return r.files()
        })).map(file => path.relative(this.root, file));
    }
    preflight(files) {
        return Promise.all(this.releasers.map(r => r.preflight(files)));
    }
    modify() {
        return Promise.all(this.releasers.map(r => r.modify()));
    }
    commit(files) {
        return Promise.all(this.releasers.map(r => r.commit(files)));
    }
}

var release = new Release([
    new ComposerRelease(tag, branch, packages),
    new VersionRelease(tag, packages),
    new ChangelogRelease(tag, packages, path.join(__dirname, '../CHANGELOG.md')),
    new NpmRelease(tag, packages),
    new GitRelease(tag, 'origin', branch, message),
], gitRoot);

const files = release.files();
release.preflight(files)
    .then(() => release.modify())
    .then(() => {
        commit
            ? release.commit(files).then(() => files)
            : Promise.resolve(files);
    })
    .then(() => {
        console.log(`${commit ? 'Committed' : 'Would commit'} changes for ${tag} to\n  - ${files.join("\n  - ")}`)
    })
    .catch(e => {console.error(e); throw e});
