import React from 'react';
import PropTypes from 'prop-types';
import './ComponentTopBar.css';

const ComponentTopBar = ({ title, selector, actions }) => {
  return (
    <div className="ComponentTopBar">
      <div className="inner">
        <h4 className="name">
          {title}
        </h4>
        <div className="component">
          {selector}
        </div>
        <ul className="actions">
          {React.Children.map(actions, (c, i) =>
            <li key={i}>
              {c}
            </li>
          )}
        </ul>
      </div>
    </div>
  );
};
ComponentTopBar.propTypes = {
  title: PropTypes.string,
  selector: PropTypes.node,
  actions: PropTypes.arrayOf(PropTypes.element)
};
ComponentTopBar.defaultProps = {
  title: '',
  selector: [],
  actions: []
};

export default ComponentTopBar;
