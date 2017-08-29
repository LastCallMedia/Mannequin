import React from 'react';
import PropTypes from 'prop-types';

const VariantSelector = ({ variants, value, onChange }) => {
  return (
    <select className="VariantSelector" value={value} onChange={onChange}>
      {variants.map(variant =>
        <option key={variant.id} value={variant.id}>
          {variant.name}
        </option>
      )}
    </select>
  );
};
VariantSelector.propTypes = {
  /** An array of variant objects. */
  variants: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      name: PropTypes.string
    })
  ),
  /** The current value of the selector */
  value: PropTypes.string,
  /** A function to call when changed. */
  onChange: PropTypes.func
};
VariantSelector.defaultProps = {
  variants: [],
  value: '',
  onChange: () => {}
};

export default VariantSelector;
