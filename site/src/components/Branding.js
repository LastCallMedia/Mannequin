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
      <div className="top">
        <Logo className="logo" />
        <h4 className="name">Mannequin</h4>
      </div>
      {slogan && (
        <h3 className="slogan">
          <span>A Component Theming Tool for the Web</span>
        </h3>
      )}
    </div>
  )
}

Branding.propTypes = {
  tiny: PropTypes.bool,
  dark: PropTypes.bool,
  large: PropTypes.bool,
  slogan: PropTypes.bool,
}
