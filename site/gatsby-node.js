const path = require('path')

exports.createPages = ({ boundActionCreators, graphql }) => {
  const { createPage } = boundActionCreators

  const docsTemplate = path.resolve(`src/templates/docs.js`)
  return graphql(`
    {
      allMarkdownRemark(
        sort: { order: DESC, fields: [frontmatter___title] }
        limit: 1000
        filter: { fields: { hidden: { ne: true } } }
      ) {
        edges {
          node {
            id
            fields {
              slug
              extension
            }
          }
        }
      }
    }
  `).then(result => {
    if (result.errors) {
      return Promise.reject(result.errors)
    }
    result.data.allMarkdownRemark.edges.forEach(({ node }) => {
      createPage({
        path: node.fields.slug,
        component: docsTemplate,
        context: {
          id: node.id,
          extension: node.fields.extension,
        },
      })
    })
  })
}

/**
 * Manipulate the URL for nodes as they enter the system.
 */
exports.onCreateNode = ({ node, boundActionCreators, getNode }) => {
  const { createNodeField } = boundActionCreators
  if (node.internal.type === 'File') {
    // Set up a slug field on the file.  This lets us do alias lookups during markdown
    // parsing.
    if (node.sourceInstanceName === 'extensions') {
      createNodeField({
        node,
        name: `slug`,
        value: createExtensionDocSlug(node),
      })
    } else {
      createNodeField({ node, name: `slug`, value: createSlug(node) })
    }
  } else if (
    node.internal.type === 'MarkdownRemark' &&
    getNode(node.parent).internal.type === 'File'
  ) {
    const fileNode = getNode(node.parent)
    let hidden = false

    if (isReadme(fileNode)) {
      enrichReadme(node, fileNode, boundActionCreators)
    }
    createNodeField({ node, name: 'ghEditUrl', value: getEditUrl(fileNode) })
    createNodeField({ node, name: 'extension', value: getExtension(fileNode) })
    createNodeField({ node, name: 'hidden', value: getHidden(fileNode) })
    createNodeField({
      node,
      name: 'menuTitle',
      value: getMenuTitle(node, fileNode),
    })
    createNodeField({
      node,
      name: 'isExtensionRoot',
      value: isReadme(fileNode),
    })
    createNodeField({ node, name: 'weight', value: getWeight(node, fileNode) })
    // Copy slug field from file.
    createNodeField({ node, name: 'slug', value: fileNode.fields.slug })
  }
}

const isReadme = ({ relativePath }) =>
  'README.md' === path.posix.basename(relativePath)
const isChangeLog = ({ relativePath }) =>
  'CHANGELOG.md' === path.posix.basename(relativePath)
const getExtension = ({ relativePath }) => relativePath.split('/')[0]
const getHidden = fileNode => isChangeLog(fileNode)

function getMenuTitle(node, fileNode) {
  if (isReadme(fileNode)) {
    return 'Overview'
  }
  return node.frontmatter.title
}

function getWeight(node, fileNode) {
  if (isReadme(fileNode)) {
    return -1
  }
  return node.frontmatter.weight || 0
}

function getEditUrl({ relativePath }) {
  return `https://github.com/LastCallMedia/Mannequin/edit/master/src/${
    relativePath
  }`
}

const createSlug = ({ relativePath }) => {
  return '/' + relativePath.replace(/\.md/, '/').toLowerCase()
}

const createExtensionDocSlug = fileNode => {
  const extension = getExtension(fileNode).toLowerCase()
  if (isReadme(fileNode)) {
    // README.md acts as index.
    return `/extensions/${extension}/`
  }
  if (isChangeLog(fileNode)) {
    return `/extensions/${extension}/changes`
  }
  const { relativePath } = fileNode
  const trail = relativePath
    .replace(/\.md/, '/')
    .split('/')
    .slice(2)
    .join('/')
  return `/extensions/${extension}/${trail}`
}

const enrichReadme = (markdownNode, fileNode, { createNodeField }) => {
  const dir = path.posix.dirname(fileNode.absolutePath)
  const composerFile = `${dir}/composer.json`
  const composer = require(composerFile)
  markdownNode.frontmatter.title = getExtension(fileNode)
  markdownNode.frontmatter.description = composer.description
}
