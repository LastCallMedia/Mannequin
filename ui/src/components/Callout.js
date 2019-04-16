import React from 'react';
import PropTypes from 'prop-types';
// import './Callout.css';
import cx from 'classnames';

const Callout = ({ type, title, content }) => (
  <div className={cx('callout', type)}>
    {title && <h3>{title}</h3>}
    {content}
  </div>
);

Callout.propTypes = {
  type: PropTypes.string,
  title: PropTypes.node,
  content: PropTypes.node
};

export default Callout;
