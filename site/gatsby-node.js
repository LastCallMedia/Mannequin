
const path = require('path');

exports.createPages = ({ boundActionCreators, graphql }) => {
    const { createPage } = boundActionCreators;

    const docsTemplate = path.resolve(`src/templates/docs.js`);
    return graphql(`{
    allMarkdownRemark(
      sort: { order: DESC, fields: [frontmatter___title] }
      limit: 1000
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

/**
 * Manipulate the URL for nodes as they enter the system.
 */
exports.onCreateNode = ({node, boundActionCreators, getNode}) => {
    const {createNodeField} = boundActionCreators;
    if(node.internal.type === 'File') {
        createNodeField({ node, name: `slug`, value: createSlug(node) })
    }
    else if(node.internal.type === 'MarkdownRemark' && getNode(node.parent).internal.type === 'File') {
        const fileNode = getNode(node.parent)
        createNodeField({ node, name: `slug`, value: createSlug(fileNode) })
    }
}

const createSlug = (fileNode) => {
    switch(fileNode.sourceInstanceName) {
        case 'docs':
            return `/docs/${fileNode.relativePath.replace(/\.md$/, '')}`;
        case 'extensions':
            return `/extensions/${fileNode.relativePath.replace(/\.md$/, '')}`;
    }
}