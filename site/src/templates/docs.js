import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'
import MenuTree from '../components/MenuTree';

export default function Template(props) {
  const { markdownRemark: post, allMarkdownRemark: nav } = props.data
  const sidebar = nav.edges.length
      ? buildSidebar(nav.edges, post.headings, post.id)
      : null
  return (
    <PageWrapper
      title={post.frontmatter.title}
      description={post.frontmatter.description}
    >
      <Page title={post.frontmatter.title} sidebar={sidebar}>
        <div dangerouslySetInnerHTML={{ __html: post.html }} />
      </Page>
    </PageWrapper>
  )
}

export const pageQuery = graphql`
  query PageByPath($id: String!, $extension: String!) {
    markdownRemark(id: {eq: $id}) {
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
    }
    allMarkdownRemark(
      filter: {
        fields: {
          extension: {eq: $extension},
          hidden: {ne: true}
        },
      },
      
    ) {
      edges {
        node {
          fields {
            slug
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

function buildSidebar(nav, headings, currId) {
  const tree = nav.map(({node}) => {
      let below = []
      if(node.id === currId) {
          below = headings.map(heading => {
              return {title: heading.value, to: anchor(heading.value), below: []}
          })
      }
      return {title: node.frontmatter.title, to: node.fields.slug, below}
  })

  return <MenuTree links={tree} />
}

const anchor = value => `#${value.toLowerCase().replace(' ', '-')}`
