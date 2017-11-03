const select = require('unist-util-select');
const {parse} = require('url');
const path = require('path');

module.exports = function({ files, markdownNode, markdownAST, pathPrefix, getNode }, pluginOptions) {
    // get the original filename of this file.
    const absPath = getNode(markdownNode.parent).absolutePath;

    function findFile(filename) {
        return files.filter(file => file.absolutePath === filename).pop();
    }

    select(markdownAST, 'link')
        .forEach(function(el) {
            let url = parse(el.url);
            // Rewrite markdown links.
            if(!url.hostname && url.pathname && url.pathname.match(/\.md$/)) {
                // resolve the absolute path to the file that's being referenced.
                const referencedPath = path.resolve(path.dirname(absPath), url.pathname);
                // look up the slug of that original file, or throw an error if it can't be found.
                const referencedFile = findFile(referencedPath);
                if(referencedFile) {
                    url.pathname = referencedFile.fields.slug;
                }
                else {
                    // Note the missing file.
                    console.error('Unable to find file ' + referencedPath);
                    // Try just dropping the .md suffix.
                    url.pathname = url.pathname.replace(/\.md$/, '/');
                }

                el.url = url.format();
            }
        });



}