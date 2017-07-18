
import React from 'react';
import PropTypes from 'prop-types';
import {PatternShape, VariantShape} from '../types';
import VariantSelector from './VariantSelector';
import {OpenNew} from '../Icon';
import './PatternTopBar.css';

const PatternTopBar = ({pattern, variant, openWindow, toggleInfo, changeVariant}) => {
    return (
        <div className="PatternTopBar">
            <div className="inner">
                <h4 className="name">{pattern.name}</h4>
                <div className="variant"><VariantSelector variants={pattern.variants} selected={variant.id} changeVariant={changeVariant} /></div>
                <ul className="actions">
                    <li><button onClick={toggleInfo} className="PatternInfoButton">View Pattern Info</button></li>
                    <li><a onClick={openWindow} target="_blank" className="OpenWindowButton" href={variant.rendered}><OpenNew /></a></li>
                </ul>
            </div>
        </div>
    )
}
PatternTopBar.propTypes = {
    pattern: PropTypes.shape(PatternShape),
    variant: PropTypes.shape(VariantShape),
    openWindow: PropTypes.func.isRequired,
    toggleInfo: PropTypes.func.isRequired,
    changeVariant: PropTypes.func.isRequired,
}

export default PatternTopBar;