
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {createSelector} from 'reselect';
import {PatternList} from './Pattern';

class TypePage extends Component {
  render() {
    const {patterns, type} = this.props;
    return (
      <main className="TypePage">
        <h1 className="page-title">{type}</h1>
        <PatternList patterns={patterns} />
      </main>
    )
  }
}

const getPatterns = state => state.patterns;
const getSelectedType = (state, ownProps) => ownProps.match.params.type;
const getTypePatterns = createSelector(
  [getPatterns, getSelectedType],
  (patterns, type) => {
    return patterns.filter(p => p.tags['type'] === type)
  }
)

const mapStateToProps = (state, ownProps) => {
  return {
    patterns: getTypePatterns(state, ownProps),
    type: getSelectedType(state, ownProps),
    tags: state.tags
  }
}


export default connect(mapStateToProps)(TypePage);