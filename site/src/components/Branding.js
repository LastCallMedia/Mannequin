import React from 'react'
import Logo from '!svg-react-loader?name=Logo!../img/Logo.svg'
import PropTypes from 'prop-types';
import './Branding.scss';

export default function Branding({tiny = false, dark = false, large = false,slogan = false}) {
  return (
    <div
      className={`Branding${tiny ? ' tiny' : ''}${dark ? ' dark' : ''}${large
        ? ' large'
        : ''}`}
    >
      <Logo className="logo" />
      <span className="separator"></span>
      <div className="right">
        <h2 className="name">Mannequin</h2>
          {slogan && <h4 className="slogan">A Component Theming Tool for the Web</h4>}
      </div>
    </div>
  )
}

Branding.propTypes = {
  tiny: PropTypes.bool,
  dark: PropTypes.bool,
  large: PropTypes.bool,
  slogan: PropTypes.bool,
}
