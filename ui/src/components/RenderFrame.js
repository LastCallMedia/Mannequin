
import React from 'react';
import PropTypes from 'prop-types';

const RenderFrame = ({src}) => {
    return (
        <iframe title="Rendered Pattern" frameBorder="0" src={src}></iframe>
    )
}
RenderFrame.propTypes = {
    src: PropTypes.string.isRequired
}

export default RenderFrame;