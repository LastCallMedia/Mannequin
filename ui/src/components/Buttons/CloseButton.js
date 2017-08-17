
import React from 'react';
import PropTypes from 'prop-types';
import {Close} from '../../Icon';
import cx from 'classnames';
import './CloseButton.css';

const CloseButton = ({className, ...rest}) => (
    <button className={cx('CloseButton', className)} {...rest}><Close/></button>
)

CloseButton.propTypes = {
    className: PropTypes.string
}

export default CloseButton;