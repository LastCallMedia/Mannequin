
var GitRelease = require('./git');
var ComposerRelease = require('./composer');
var ChangelogRelease = require('./changelog.js');
var NpmRelease = require('./npm');

var globby = require('globby');
var path = require('path');

const tag = process.argv[2];
const branch = process.argv[3] || 'changelog';

if(!tag || !tag.match(/(v?)\d+\.\d+\.\d+(-(alpha|beta|rc)\d+)?/)) {
    throw new Error('Invalid tag string: ' + tag);
}
var packages = [
    {name: 'Core', changelog: path.join(__dirname, '../src/Core/CHANGELOG.md'), composer: path.join(__dirname, '../src/Core/composer.json')},
    {name: 'Html', changelog: path.join(__dirname, '../src/Html/CHANGELOG.md'), composer: path.join(__dirname, '../src/Html/composer.json')},
    {name: 'Twig', changelog: path.join(__dirname, '../src/Twig/CHANGELOG.md'), composer: path.join(__dirname, '../src/Twig/composer.json')},
    {name: 'Drupal', changelog: path.join(__dirname, '../src/Drupal/CHANGELOG.md'), composer: path.join(__dirname, '../src/Drupal/composer.json')},
    {name: 'Demo', changelog: path.join(__dirname, '../demo/CHANGELOG.md'), composer: path.join(__dirname, '../demo/composer.json')},
    {name: 'Site', changelog: path.join(__dirname, '../site/CHANGELOG.md'), npm: path.join(__dirname, '../site/package.json')},
    {name: 'Ui', changelog: path.join(__dirname, '../ui/CHANGELOG.md'), npm: path.join(__dirname, '../ui/package.json')},
];


class Release {
    constructor(releasers) {
        this.releasers = releasers;
    }
    files() {
        return [].concat(...this.releasers.map(r => {
            return r.files()
        }));
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
    new ChangelogRelease(tag, packages, path.join(__dirname, '../CHANGELOG.md')),
    new NpmRelease(tag, packages),
    new GitRelease(tag, 'origin', branch),
]);

const files = release.files();
release.preflight(files)
    .then(() => release.modify())
    .then(() => release.commit(files))
    .catch(e => {console.error(e); throw e});
