import React from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';
import { MannySmall } from './Icons';
import './TopBar.css';

const TopBar = ({ toggleNav }) => {
  return (
    <nav className="MannequinTopBar">
      <div className="inner">
        <Link to="/" className="logo">
          <MannySmall />
        </Link>
        <div className="title" />
        <button className="drawer-toggle opener" onClick={toggleNav}>
          Menu <i className="fas fa-bars" />
        </button>
      </div>
    </nav>
  );
};
TopBar.propTypes = {
  toggleNav: PropTypes.func
};

export default TopBar;
