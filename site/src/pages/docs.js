import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'
import Link from 'gatsby-link'

export default function Template(props) {
  const pages = props.data.allMarkdownRemark
  return (
    <PageWrapper
      title="Docs"
      description={'Read the documentation for Mannequin'}
    >
      <Page title="Docs">
        <p>Browse the documentation</p>
        <ul className="CollectionIndex">
          {pages.edges.map(page => (
            <li key={page.node.id}>
              <Link to={page.node.fields.slug}>
                <h3>{page.node.frontmatter.title}</h3>
                <p>{page.node.frontmatter.description}</p>
              </Link>
            </li>
          ))}
        </ul>
      </Page>
    </PageWrapper>
  )
}

export const pageQuery = graphql`
  query DocsIndex {
    allMarkdownRemark(
      sort: { order: DESC, fields: [frontmatter___title] }
      limit: 3
      filter: { fields: { slug: { regex: "/docs/" } } }
    ) {
      edges {
        node {
          id
          fields {
            slug
          }
          frontmatter {
            title
            description
          }
        }
      }
    }
  }
`
