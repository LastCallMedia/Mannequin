const select = require('unist-util-select')
const path = require('path')
const parse = require('url').parse
const fs = require('fs')

/**
 * This bare-bones plugin replaces links with the link text @see
 * with a code snippet of the content of files they reference.
 *
 * It doesn't do error handling, so use at your own risk.
 */
module.exports = function(
  { files, markdownNode, markdownAST, pathPrefix, getNode },
  pluginOptions
) {
  // get the original filename of this file.
  const absPath = getNode(markdownNode.parent).absolutePath

  select(markdownAST, 'link').forEach(function(el) {
    if (select(el, 'text[value^="@see"]', el).length) {
      const url = parse(el.url)
      const filename = path.resolve(path.dirname(absPath), url.path)
      const file = getFile(filename, url.hash)

      el.type = 'code'
      el.lang = file.lang
      el.value = splitFile(file.contents, url.hash ? url.hash.slice(1) : '')
    }
  })
}

function getFileLang(filename) {
  switch (path.extname(filename)) {
    case '.php':
      return 'php'
    case '.js':
      return 'js'
    case '.twig':
      return 'twig'
  }
}

function getFile(filename, lineSpec) {
  return {
    lang: getFileLang(filename),
    contents: fs.readFileSync(filename, 'UTF-8'),
  }
}

function splitFile(contents, lineSpec) {
  var matches
  if ((matches = lineSpec.match(/^L(\d+)(-(\d+))?$/))) {
    // Account for 0 based indices by reducing by 1.
    const start = parseInt(matches[1]) - 1
    const end = parseInt(matches[3] || matches[1]) - 1
    return contents
      .split('\n')
      .slice(start, end)
      .join('\n')
  }
  return contents
}
