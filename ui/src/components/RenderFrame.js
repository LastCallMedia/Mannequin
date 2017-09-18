import React from 'react';
import PropTypes from 'prop-types';
import './RenderFrame.css';

const RenderFrame = ({ src }) => {
  return (
    <iframe
      className="RenderFrame"
      title="Rendered Component"
      frameBorder="0"
      src={src}
    />
  );
};
RenderFrame.propTypes = {
  src: PropTypes.string.isRequired
};

export default RenderFrame;
