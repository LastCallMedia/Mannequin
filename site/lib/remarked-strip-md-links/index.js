const select = require('unist-util-select')
const { parse } = require('url')
const path = require('path')

module.exports = function(
  { files, markdownNode, markdownAST, pathPrefix, getNode },
  pluginOptions
) {
  // get the original filename of this file.
  const absPath = getNode(markdownNode.parent).absolutePath

  function findFile(filename) {
    return files.filter(file => file.absolutePath === filename).pop()
  }
  function getRepoRelativePath(absPath) {
    return path.relative(path.resolve(__dirname, '../../../'), absPath)
  }

  select(markdownAST, 'link').forEach(function(el) {
    let url = parse(el.url)
    // Rewrite markdown links.
    if (!url.hostname && url.pathname) {
      // resolve the absolute path to the file that's being referenced.
      const referencedPath = path.resolve(
        path.dirname(absPath),
        url.pathname.replace(/^\/+/, '')
      )

      if (url.pathname.match(/\.md$/)) {
        // look up the slug of that original file, or throw an error if it can't be found.
        const referencedFile = findFile(referencedPath)
        if (referencedFile) {
          url.pathname = referencedFile.fields.slug
        } else {
          console.error('Unable to find file ' + referencedPath)
        }
      } else {
        // If it's not a markdown file, attempt to link it directly to the repo.
        const repoRelative = getRepoRelativePath(referencedPath)
        let ghUrl = parse(
          `https://github.com/LastCallMedia/Mannequin/tree/master/${
            repoRelative
          }`
        )
        ghUrl.hash = url.hash
        ghUrl.search = url.search
        ghUrl.query = url.query
        url = ghUrl
      }
      el.url = url.format()
    }
  })
}
