import React from 'react';
import PropTypes from 'prop-types';
import './Icon.css';

const Close = ({ color }) => {
  const style = color ? { fill: color, stroke: color } : {};
  return (
    <svg
      className="Icon Close"
      style={style}
      x="0px"
      y="0px"
      viewBox="0 0 1000 1000"
    >
      <g>
        <path d="M849.9,990L990,849.9L639.9,499.9L990,150L849.9,10L499.9,359.9L150,10L10,150l349.9,349.9L10,849.9L150,990l349.9-350.1L849.9,990z" />
      </g>
    </svg>
  );
};
Close.propTypes = {
  color: PropTypes.string
};
Close.defaultProps = {
  color: 'black'
};

export default Close;
