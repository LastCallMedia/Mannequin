import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'
import Link from 'gatsby-link'

export default function Template(props) {
  const pages = props.data.allMarkdownRemark
  return (
    <PageWrapper
      title="Extensions"
      description={'A list of Mannequin extensions'}
    >
      <Page title="Extensions">
        <p>Browse the extensions</p>
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
  query ExtensionIndex {
    allMarkdownRemark(
      sort: { order: DESC, fields: [frontmatter___title] }
      limit: 3
      filter: { fields: { slug: { regex: "/extensions/" } } }
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
