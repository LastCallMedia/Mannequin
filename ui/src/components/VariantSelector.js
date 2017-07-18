
import React from 'react';
import PropTypes from 'prop-types';
import {VariantShape} from '../types';

const VariantSelector = ({variants, selected, changeVariant}) => (
    <select className="VariantSelector" value={selected} onChange={e => changeVariant(e.target.value)}>
        {variants.map(variant => (
            <option key={variant.id} value={variant.id}>{variant.name}</option>
        ))}
    </select>
)
VariantSelector.propTypes = {
    variants: PropTypes.arrayOf(PropTypes.shape(VariantShape)),
    selected: VariantShape.id,
    changeVariant: PropTypes.func.isRequired,
}

export default VariantSelector;