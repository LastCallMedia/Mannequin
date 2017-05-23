
import React from 'react';

export function PatternRenderDisplay(props) {
  let {pattern} = props;
  return (<iframe title="Pattern Render" className="pattern-render" frameBorder="0" src={pattern.rendered}></iframe>)
}

export function PatternSourceDisplay(props) {
  let {pattern} = props;
  return (<iframe title="Pattern Source" className="pattern-source" frameBorder="0" src={pattern.source}></iframe>);
}