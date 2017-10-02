import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'

export default function Template(props) {
  const { markdownRemark: post } = props.data
  const sidebar =
    post.headings && post.headings.length > 0
      ? buildSidebar(post.headings)
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
    }
  }
`

function buildSidebar(headings) {
  return (
    <ul className="menu vertical">
      {headings.map(heading => (
        <li key={heading.value} className={`level-${heading.level}`}>
          <a href={`${anchor(heading.value)}`}>{heading.value}</a>
        </li>
      ))}
    </ul>
  )
}

const anchor = value => `#${value.toLowerCase().replace(' ', '-')}`
