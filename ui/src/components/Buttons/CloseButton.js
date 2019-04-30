import React from 'react';
import PropTypes from 'prop-types';
import { Close } from '../Icons';
import cx from 'classnames';
// import './CloseButton.css';

const CloseButton = ({ className, ...rest }) => (
  // <button className={cx('CloseButton', className)} {...rest}>
  //   <Close color="black" />
  // </button>
  <button className={cx('CloseButton', className)} {...rest}>
    <span>Close</span> <i className="fas fa-times"></i>
  </button>
);

CloseButton.propTypes = {
  className: PropTypes.string
};

export default CloseButton;
