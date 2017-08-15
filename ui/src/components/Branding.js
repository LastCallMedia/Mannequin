
import React from 'react';
import logo from '../svg/manny_wave.svg';

import './Branding.css';

/**
 * Homepage branding block.
 */
const Branding = () => (
    <div className="Branding">
        <div className="top">
            <img src={logo} className="logo" alt="Mannequin Logo" />
            <h1>Mannequin</h1>
        </div>
        <h3><span>A Component Theming Tool for the Web</span></h3>
    </div>
)

export default Branding;