import React from 'react';
import Callout from './Callout';
import PropTypes from 'prop-types';

const PatternProblems = ({ problems }) => {
  return (
    <Callout
      type="alert"
      title="There were problems found with this pattern!"
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

PatternProblems.propTypes = {
  problems: PropTypes.arrayOf(PropTypes.node)
};
PatternProblems.defaultProps = {
  problems: []
};
export default PatternProblems;
