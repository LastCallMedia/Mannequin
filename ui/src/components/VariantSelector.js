
import React from 'react';
import PropTypes from 'prop-types';
import {VariantShape} from '../types';

const VariantSelector = ({variants, value, onChange}) => {
    return (
        <select value={value} onChange={onChange}>
            {variants.map(variant => (
                <option key={variant.id} value={variant.id}>{variant.name}</option>
            ))}
        </select>
    )
}
VariantSelector.propTypes = {
    variants: PropTypes.arrayOf(PropTypes.shape(VariantShape)),
    value: PropTypes.string,
    onChange: PropTypes.func,
}

export default VariantSelector;