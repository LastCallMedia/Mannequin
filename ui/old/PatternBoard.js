
import React, {Component} from 'react';

import {PatternCard} from './PatternCard';

class PatternBoard extends Component {
  render() {
    let {patterns, grouping, tags} = this.props;

    var groupingBasis = tags[grouping] ? Object.keys(tags[grouping]) : [];
    let groups = groupPatterns(patterns, grouping, groupingBasis);
    var sortGroupsCb = tags[grouping] ? sortGroups(Object.keys(tags[grouping])) : () => {};

    return (
      <div className="PatternBoard">
        <div className="PatternBoard-inner row" data-columns={Object.keys(groups).length}>
          {Object.keys(groups).sort(sortGroupsCb).map(tid => (
            <PatternBoardCol key={`pb:${grouping}:${tid}`} tagId={tid} tagName={getTagLabel(grouping, tid, tags)} patterns={groups[tid]} />
          ))}
        </div>
      </div>
    );
  }
}

function PatternBoardCol(props) {
  let {tagId, tagName, patterns} = props;
  return (
    <div key={tagId} className="PatternBoard-col columns">
      <h3>{tagName}</h3>
      <ul className="PatternBoard-list no-bullet">
        {patterns.map(p => {
          return (
            <li key={`pbl:${tagId}:${p.id}`}><PatternCard pattern={p} /></li>
          )
        })}
      </ul>
    </div>
  )
}

function groupPatterns(patterns, grouping) {
  return patterns.reduce((acc, p) => {
    var thisGroup = p.tags[grouping] ? p.tags[grouping] : 'unknown';
    if(!acc.hasOwnProperty(thisGroup)) {
      acc[thisGroup] = [];
    }
    acc[thisGroup].push(p);
    return acc;
  }, {});
}

function sortGroups(groupTags) {
  var weights = groupTags;
  return function(a, b) {
    var aWeight = weights.indexOf(a);
    var bWeight = weights.indexOf(b);
    return aWeight - bWeight;
  }
}

function getTagLabel(grouping, tag, tags) {
  return tags[grouping] && tags[grouping][tag] ? tags[grouping][tag] : 'Unknown';
}
export default PatternBoard;