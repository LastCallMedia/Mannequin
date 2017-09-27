import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'
import Link from 'gatsby-link'

export default function AboutPage() {
  return (
    <PageWrapper
      title="About"
      description={'An explanation of what Mannequin is'}
    >
      <Page title="About">
        <p>
          Mannequin is a themer's tool. It's intended to allow rapid, focused
          development on individual templates (components) in isolation from all
          the other craziness that comes along with developing a dynamic
          website.
        </p>
        <p>
          Specifically, Mannequin thinks in terms of individual components.
          Components are chunks of markup that can stand on their own. They may
          be composed of other components, but they can be thought of as
          self-sufficient objects. Styling from one component should be
          self-contained and not dependent on the styling of unrelated
          components.
        </p>
        <p>
          As a tool, Mannequin's goal is to help you focus on markup and styling
          for one component at a time. We hope you like it!
        </p>
        <h3>Our Influences</h3>
        <p>
          We would be remiss if we didn't mention the excellent{' '}
          <a href="http://patternlab.io">Pattern Lab</a> as our primary
          influence. As an introduction to atomic theming, Pattern Lab is great,
          but we found that it fell a little short when it came to integrating
          with the templates we were <em>actually</em> using in our
          applications. As a result, we created this tool for internal use, and
          decided to open source it.
        </p>
        <h3>Next Steps</h3>
        <ul className="">
          <li>
            <a href="https://demo.mannequin.io">Visit the demo</a>
          </li>
          <li>
            <Link to="/">Learn how to get started</Link>
          </li>
          <li>
            <Link to="/docs">Read the docs</Link>
          </li>
        </ul>
      </Page>
    </PageWrapper>
  )
}
