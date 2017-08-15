
import React from 'react';
import PropTypes from 'prop-types';
import cx from 'classnames';
import './CloseButton.css';

const CloseButton = ({className, ...rest}) => (
    <button className={cx('CloseButton', className)} {...rest}><span aria-hidden="true">&times;</span></button>
)

CloseButton.propTypes = {
    className: PropTypes.string
}

export default CloseButton;