
import React from 'react';
import PropTypes from 'prop-types';
import './ViewInfoButton.css';

const ViewInfoButton = (props) => (
    <button className="ViewInfoButton" {...props}>View Pattern Info</button>
)

ViewInfoButton.propTypes = {
    onClick: PropTypes.func
}

export default ViewInfoButton;