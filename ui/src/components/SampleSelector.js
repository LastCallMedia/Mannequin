import React from 'react';
import PropTypes from 'prop-types';

const SampleSelector = ({ samples, value, onChange }) => {
  return (
    <select className="SampleSelector" value={value} onChange={e => onChange(e.target.value)}>
      {samples.map(sample =>
        <option key={sample.id} value={sample.id}>
          {sample.name}
        </option>
      )}
    </select>
  );
};
SampleSelector.propTypes = {
  /** An array of sample objects. */
  samples: PropTypes.arrayOf(
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
SampleSelector.defaultProps = {
  samples: [],
  value: '',
  onChange: () => {}
};

export default SampleSelector;
