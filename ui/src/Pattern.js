
import React from 'react';
import {Route, Redirect, Link} from 'react-router-dom';
import {connect} from 'react-redux';
import AppFrame from "./AppFrame";

function PatternRenderDisplay(props) {
  let {pattern} = props;
  return (<iframe title="Pattern Render" className="pattern-render" frameBorder="0" src={pattern.rendered}></iframe>)
}

function PatternSourceDisplay(props) {
  let {pattern} = props;
  return (<iframe title="Pattern Source" className="pattern-source" frameBorder="0" src={pattern.source}></iframe>);
}

function PatternDisplay(props) {
  const {patterns, match: patternMatch, history} = props;
  var selectedPattern = patterns.filter(pattern => pattern.id === patternMatch.params.id).pop();

  if(!selectedPattern) {
    return <span></span>
  }

  const controls = (
    <div>
      <Link to={`${patternMatch.url}/render`}>View</Link>
      <Link to={`${patternMatch.url}/source`}>Source</Link>
      {/* Open in new window? */}
      {/* View HTML source? */}
    </div>
  );

  return (
    <Route path={`${patternMatch.url}/render`} children={({match}) => (
      <AppFrame resizable={match} onClose={history.push.bind(history, '/')} controls={controls}>
        <Route path={`${patternMatch.url}/render`} render={() => <PatternRenderDisplay pattern={selectedPattern}/>}/>
        <Route path={`${patternMatch.url}/source`} render={() => <PatternSourceDisplay pattern={selectedPattern}/>}/>
        <Route path={patternMatch.url} exact render={() => <Redirect to={`${patternMatch.url}/render`}/>} />
      </AppFrame>
    )} />
  )
}

const mapStateToProps = (state, ownProps) => {
  return {
    patterns: state.patterns,
    tags: state.tags
  }
}

export default connect(mapStateToProps)(PatternDisplay);