import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'
import MenuTree from '../components/MenuTree'

export default function Template(props) {
  const { markdownRemark: post, allMarkdownRemark: nav } = props.data
  const menu = buildMenu(nav.edges, post.headings, post.id)
  return (
    <Page
      title={post.frontmatter.title}
      description={post.frontmatter.description}
      menu={menu}
      section={post.fields.extension}
      edit={post.fields.ghEditUrl}
    >
      <div dangerouslySetInnerHTML={{ __html: post.html }} />
    </Page>
  )
}

export const pageQuery = graphql`
  query PageByPath($id: String!, $extension: String!) {
    markdownRemark(id: { eq: $id }) {
      id
      html
      headings {
        value
        depth
      }
      frontmatter {
        title
        description
      }
      fields {
        ghEditUrl
        extension
      }
    }
    allMarkdownRemark(
      filter: {
        fields: { extension: { eq: $extension }, hidden: { ne: true } }
      }
      sort: { fields: [fields___weight] }
    ) {
      edges {
        node {
          fields {
            slug
            menuTitle
          }
          frontmatter {
            title
          }
          id
        }
      }
    }
  }
`

function buildMenu(nav, headings, currId) {
  return nav.map(({ node }) => {
    let below = []
    let active = false
    if (node.id === currId) {
      active = true
      below = headings.filter(h2Only).map(heading => {
        return { title: heading.value, to: anchor(heading.value), below: [] }
      })
    }
    return {
      title: node.fields.menuTitle,
      to: node.fields.slug,
      below,
      active,
    }
  })
}

const h2Only = heading => heading.depth === 2
const anchor = value => `#${value.toLowerCase().replace(/ /g, '-')}`
