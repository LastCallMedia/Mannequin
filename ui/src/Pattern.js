
import React from 'react';
import Resizable from 'react-resizable-box';

export function PatternRenderDisplay(props) {
  let {pattern} = props;

  return (
    <iframe className="pattern-render" frameBorder="0" src={pattern.rendered}></iframe>
  )
  return (<iframe className="pattern-render" frameBorder="0" src={pattern.rendered}></iframe>)
}

export function PatternSourceDisplay(props) {
  let {pattern} = props;
  return (<iframe className="pattern-source" frameBorder="0" src={pattern.source}></iframe>);
}