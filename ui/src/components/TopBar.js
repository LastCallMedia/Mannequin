import React from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';
import { MannySmall } from './Icons';
import Button from './Buttons/Button'
import './TopBar.css';

const TopBar = ({ toggleNav }) => {
  return (
    <nav className="MannequinTopBar">
      <div className="inner">
        <Link to="/" className="logo">
          <MannySmall />
        </Link>
        <div className="title" />
        <Button
          text="Menu"
          element="button"
          icon="menu"
          classes="Button MenuButton"
          onClick={toggleNav} />
      </div>
    </nav>
  );
};
TopBar.propTypes = {
  toggleNav: PropTypes.func
};

export default TopBar;
