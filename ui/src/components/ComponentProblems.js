import React from 'react';
import Callout from './Callout';
import PropTypes from 'prop-types';

const ComponentProblems = ({ problems }) => {
  return (
    <Callout
      type="alert"
      title="There were problems found with this component!"
      content={
        <ul>
          {problems.map((p, i) =>
            <li key={i}>
              {p}
            </li>
          )}
        </ul>
      }
    />
  );
};

ComponentProblems.propTypes = {
  problems: PropTypes.arrayOf(PropTypes.node)
};
ComponentProblems.defaultProps = {
  problems: []
};
export default ComponentProblems;
