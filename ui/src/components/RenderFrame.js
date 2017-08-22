import React from 'react';
import PropTypes from 'prop-types';
import './RenderFrame.css';

const RenderFrame = ({ src }) => {
  return (
    <iframe
      className="RenderFrame"
      title="Rendered Pattern"
      frameBorder="0"
      src={src}
    />
  );
};
RenderFrame.propTypes = {
  src: PropTypes.string.isRequired
};

export default RenderFrame;
