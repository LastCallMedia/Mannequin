import React from 'react'
import Logo from '!svg-react-loader?name=Logo!../img/Logo.svg'
import cx from 'classnames'
import Link from 'gatsby-link'
import PropTypes from 'prop-types'
import './Branding.scss'

export default function Branding({
  tiny = false,
  dark = false,
  large = false,
  slogan = false,
  to = null,
}) {
  const Wrap = to ? Link : 'div'
  const wrapProps = {
    className: cx({ Branding: true, tiny, dark, large }),
  }
  if (to) {
    wrapProps.to = to
  }
  return (
    <Wrap {...wrapProps}>
      <Logo className="logo" />
      <div className="right">
        <h2 className="name">Mannequin</h2>
        {slogan && (
          <h4 className="slogan">A Component Theming Tool for the Web</h4>
        )}
      </div>
    </Wrap>
  )
}

Branding.propTypes = {
  tiny: PropTypes.bool,
  dark: PropTypes.bool,
  large: PropTypes.bool,
  slogan: PropTypes.bool,
}
