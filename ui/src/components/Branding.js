import React from 'react';
import logo from '../svg/manny_wave.svg';

// import './Branding.css';

/**
 * Homepage branding block.
 */
const Branding = () => (
  <div className="Branding">
    <img src={logo} className="logo" alt="Mannequin Logo" />
    <span className="Branding__Text">
      <h1>Mannequin</h1>
      <span>Component Theming Tool</span>
    </span>
  </div>
);

export default Branding;
