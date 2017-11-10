
const path = require('path');

exports.createPages = ({ boundActionCreators, graphql }) => {
    const { createPage } = boundActionCreators;

    const docsTemplate = path.resolve(`src/templates/docs.js`);
    return graphql(`{
    allMarkdownRemark(
      sort: { order: DESC, fields: [frontmatter___title] }
      limit: 1000,
      filter: {
        fields:{
          hidden: {ne:true}
        }
      }
    ) {
      edges {
        node {
          html
          fields {
            slug
          }
          frontmatter {
            title
          }
        }
      }
    }
  }`
    )
        .then(result => {
            if (result.errors) {
                return Promise.reject(result.errors);
            }
            result.data.allMarkdownRemark.edges
                .forEach(({ node }) => {
                    createPage({
                        path: node.fields.slug,
                        component: docsTemplate,
                        context: {} // additional data can be passed via context
                    });
                });
        });
};

const isReadme = ({relativePath}) => 'README.md' === path.posix.basename(relativePath);

/**
 * Manipulate the URL for nodes as they enter the system.
 */
exports.onCreateNode = ({node, boundActionCreators, getNode}) => {
    const {createNodeField} = boundActionCreators;
    if(node.internal.type === 'File') {
        // Set up a slug field on the file.  This lets us do alias lookups during markdown
        // parsing.
        if(node.sourceInstanceName === 'extensions') {
            if(isReadme(node)) {
                createNodeField({ node, name: `slug`, value: createReadmeSlug(node) })
            }
            else if(node.relativePath.match('/docs/')) {
                createNodeField({ node, name: 'slug', value: createExtensionDocSlug(node) })
            }
            else {
                createNodeField({ node, name: `slug`, value: createSlug(node) })
            }
        }
        else {
            createNodeField({ node, name: `slug`, value: createSlug(node) })
        }

    }
    else if(node.internal.type === 'MarkdownRemark' && getNode(node.parent).internal.type === 'File') {
        const fileNode = getNode(node.parent)
        let hidden = false;


        if(fileNode.sourceInstanceName === 'extensions') {
            if(isReadme(fileNode)) {
                enrichReadme(node, fileNode, boundActionCreators);
            }
            else if(!fileNode.relativePath.match('/docs/')) {
                // Hide this page.
                hidden = true;
            }
        }
        // Copy slug field from file.
        createNodeField({node, name: 'slug', value: fileNode.fields.slug});
        createNodeField({node, name: 'hidden', value: hidden});
    }
}

const createSlug = ({relativePath}) => {
    return '/' + relativePath.replace(/\.md/, '/');
}

const createExtensionDocSlug = ({relativePath}) => {
    let parts = relativePath.replace(/\.md/, '').split('/');
    parts.splice(1, 1);
    return `/${parts.join('/')}/`;
}

/**
 * README.md acts as the index.
 */
const createReadmeSlug = ({relativePath}) => {
    return `/${path.posix.dirname(relativePath)}/`
}

const enrichReadme = (markdownNode, fileNode, {createNodeField}) => {
    const composerFile = `${path.posix.dirname(fileNode.absolutePath)}/composer.json`;
    const composer = require(composerFile);
    markdownNode.frontmatter.title = `Mannequin ${fileNode.relativePath.split('/')[0]}`
    markdownNode.frontmatter.description = composer.description;
}