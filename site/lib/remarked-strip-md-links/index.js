const select = require(`unist-util-select`);
const {parse} = require('url');

module.exports = function({ files, markdownNode, markdownAST, pathPrefix, getNode }, pluginOptions) {
    // Strip .md suffix off of relative links in Markdown.
    select(markdownAST, 'link')
        .forEach(function(el) {
            let url = parse(el.url);
            if(!url.hostname && url.pathname && url.pathname.match(/\.md$/)) {
                url.pathname = url.pathname.replace(/\.md$/, '/');
                el.url = url.format();
            }
        });
}