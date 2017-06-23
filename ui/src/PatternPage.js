
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {createSelector} from 'reselect';
import {PatternWrapper} from "./Pattern";

class PatternPage extends Component {
  render() {
    const {pattern} = this.props;
    return (
      <main className="PatternPage">
        <h1 className="page-title">{pattern && pattern.name}</h1>
        {pattern && <PatternWrapper pattern={pattern} />}
      </main>
    );
  }
}

const getPatterns = state => state.patterns;
const getSelectedPatternId = (state, ownProps) => ownProps.match.params.pattern;

const getPattern = createSelector(
  [getPatterns, getSelectedPatternId],
  (patterns, patternId) => {
    return patterns.filter(p => p.id === patternId).pop();
  }
)

const mapStateToProps = (state, ownProps) => {
  return {
    pattern: getPattern(state, ownProps),
  }
}


export default connect(mapStateToProps)(PatternPage);