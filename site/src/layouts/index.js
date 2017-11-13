import React from 'react'
import PropTypes from 'prop-types'
import Helmet from 'react-helmet'

import '../../../site/src/scss/main.scss'

const TemplateWrapper = props => {
  const { children } = props
  return (
    <div>
      <Helmet title="Mannequin">
        <link
          rel="icon"
          type="image/png"
          sizes="16x16"
          href="/favicon-16x16.png"
        />
        <link
          rel="icon"
          type="image/png"
          sizes="32x32"
          href="/favicon-32x32.png"
        />
        <link
          rel="icon"
          type="image/png"
          sizes="96x96"
          href="/favicon-96x96.png"
        />
        <link
          rel="apple-touch-icon"
          sizes="180x180"
          href="/apple-touch-icon.png"
        />
        <html lang="en" />
      </Helmet>
      {children()}
    </div>
  )
}

TemplateWrapper.propTypes = {
  children: PropTypes.func,
}

export default TemplateWrapper
