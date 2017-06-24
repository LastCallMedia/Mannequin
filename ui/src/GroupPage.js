
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {createSelector} from 'reselect';
import {PatternList} from './Pattern';

class GroupPage extends Component {
  render() {
    const {patterns, group} = this.props;
    return (
      <main>
        <h1 className="page-title">{group}</h1>
        <PatternList patterns={patterns} />
      </main>
    )
  }
}

const getPatterns = state => state.patterns;
const getSelectedType = (state, ownProps) => ownProps.match.params.type;
const getSelectedGroup = (state, ownProps) => ownProps.match.params.group;

const getGroupTypePatterns = createSelector(
  [getPatterns, getSelectedType, getSelectedGroup],
  (patterns, type, group) => {
    return patterns.filter(p => p.tags['type'] === type && p.tags['group'] === group)
  }
)

const mapStateToProps = (state, ownProps) => {
  return {
    patterns: getGroupTypePatterns(state, ownProps),
    group: getSelectedGroup(state, ownProps),
    tags: state.tags
  }
}


export default connect(mapStateToProps)(GroupPage);