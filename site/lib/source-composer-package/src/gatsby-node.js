
const chokidar  = require('chokidar');

const { createId, createFileNode } = require(`gatsby-source-filesystem/create-file-node`);

exports.sourceNodes = (
    {bound}
);

exports.sourceNodes = (
    { boundActionCreators, getNode, hasNodeChanged, reporter },
    pluginOptions
) => {
    const { createNode, deleteNode } = boundActionCreators

    let ready = false

    const watcher = chokidar.watch(pluginOptions.path, {
        ignored: [
            `**/*.un~`,
            `**/.gitignore`,
            `**/.npmignore`,
            `**/.babelrc`,
            `**/yarn.lock`,
            `**/node_modules`,
            `../**/dist/**`,
        ],
    })

    const