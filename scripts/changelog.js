
var cl = require('keepachangelog');

function split(changelog, name) {
    var filter = lineFilter(name);
    var packageChangelog = cl.parse('');
    packageChangelog.prelude = changelog.prelude;
    packageChangelog.epilogue = changelog.epilogue;
    packageChangelog.releases = changelog.releases
        .map(r => Object.assign({}, r))
        .map(r => {
            Object.keys(r).filter(k => k[0] === k[0].toUpperCase()).forEach(k => {
                r[k] = r[k].filter(filter);
                // Remove sections that have no items.
                if(r[k].length === 0) {
                    delete r[k];
                }
            });
            return r;
        })
    return packageChangelog
}

function lineFilter(name) {
    return (line) => {
        var matches = line[0].match(/^\(([^)]+)\)/);
        var linePackages = matches.length > 1
            ? matches[1].split(',').map(p => p.trim())
            : [];
        return linePackages.includes(name);
    }
}

function bumpChangelog(version) {
    return (changelog) => {
        var upcoming = changelog.getRelease('upcoming');
        upcoming.date = new Date().toISOString();
        upcoming.version = version;
        upcoming.title = version;
        changelog.releases.unshift({version: 'upcoming'});
        return changelog
    }
}

function hasChangelog(pack) {
    return !!pack.changelog;
}

class ChangelogRelease {
    constructor(tag, packages, root) {
        this.tag = tag;
        this.packages = packages;
        this.root = root;
    }
    files() {
        var changelogs = this.packages.filter(hasChangelog).map(pack => pack.changelog);
        changelogs.unshift(this.root);
        return changelogs;
    }
    preflight() {
        // Make sure the changelog is readable and does not already
        // contain the release we're trying to create.
        return cl.read(this.root)
            .then(changelog => {
                if(changelog.getRelease(this.tag)) {
                    throw new Error(`Changelog already has release ${this.tag}`);
                }
            })
    }
    modify() {
        return cl.read(this.root)
        // Bump the changelog to a new version and add an upcoming section.
            .then(bumpChangelog(this.tag))
            .then(changelog => {
                // Write the root changelog back
                return changelog
                    .write(this.root)
                    .then(() => changelog)
            })
            .then(changelog => {
                // Split the changelog by packages
                return Promise.all(this.packages.filter(hasChangelog).map(pack => {
                    return Promise.resolve(split(changelog, pack.name))
                        .then(pcl => pcl.write(pack.changelog));
                }))
            })
    }
    commit(files) {
        return Promise.resolve();
    }
}

module.exports = ChangelogRelease;