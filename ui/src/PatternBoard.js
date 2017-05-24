
import React, {Component} from 'react';

import {PatternCard} from './PatternCard';

class PatternBoard extends Component {
  render() {
    let {patterns, grouping} = this.props;

    let groups = groupPatterns(patterns, grouping);
    let innerW = (Object.keys(groups).length / 3) * 100;
    return (
      <div className="PatternBoard">
        <div className="PatternBoard-inner row" style={{width: `${innerW}vw`}}>
          {Object.keys(groups).map(tn => (
            <PatternBoardCol key={`pb:${tn}`} tagId={tn} tagName={tn} patterns={groups[tn]} />
          ))}
        </div>
      </div>
    );
  }
}

function PatternBoardCol(props) {
  let {tagId, tagName, patterns} = props;
  return (
    <div key={tagId} className="PatternBoard-col columns small-3">
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
    var thisGroup = p.tags[grouping] ? p.tags[grouping] : 'Unknown';
    if(!acc.hasOwnProperty(thisGroup)) {
      acc[thisGroup] = [];
    }
    acc[thisGroup].push(p);
    return acc;
  }, {});
}
export default PatternBoard;