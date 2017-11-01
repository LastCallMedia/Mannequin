import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'

export default function Template(props) {
  const { markdownRemark: post } = props.data
  const sidebar =
    post.headings && post.headings.length > 0
      ? buildSidebar(post.headings)
      : null
  const edit = buildEditLink(post.parent.relativePath);
  return (
    <PageWrapper
      title={post.frontmatter.title}
      description={post.frontmatter.description}
    >
      <Page title={post.frontmatter.title} sidebar={sidebar} editLink={edit}>
        <div dangerouslySetInnerHTML={{ __html: post.html }} />
      </Page>
    </PageWrapper>
  )
}

export const pageQuery = graphql`
  query PageByPath($path: String!) {
    markdownRemark(fields: { slug: { eq: $path } }) {
      html
      headings {
        value
        depth
      }
      frontmatter {
        title
        description
      }
      parent {
        ... on File {
          relativePath
        }
      }
    }
  }
`

function buildSidebar(headings) {
  return (
    <ul className="PageMenu">
      {headings.map(heading => (
        <li key={heading.value} className={`level-${heading.depth}`}>
          <a href={`${anchor(heading.value)}`}>{heading.value}</a>
        </li>
      ))}
    </ul>
  )
}

function buildEditLink(relativePath) {
  return (
      <a className="EditDoc" href={`https://github.com/LastCallMedia/Mannequin/edit/master/docs/${relativePath}`}>Edit</a>
  )
}

const anchor = value => `#${value.toLowerCase().replace(' ', '-')}`
