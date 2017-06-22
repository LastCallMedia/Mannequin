
import React from 'react';
import {Route, Redirect, Link} from 'react-router-dom';
import {connect} from 'react-redux';
import AppFrame from "./AppFrame";

function PatternRenderDisplay(props) {
  let {pattern, set} = props;
  return (<iframe title="Pattern Render" className="pattern-render" frameBorder="0" src={pattern.rendered}></iframe>)
}

function PatternSourceDisplay(props) {
  let {pattern} = props;
  return (<iframe title="Pattern Source" className="pattern-source" frameBorder="0" src={pattern.source}></iframe>);
}

function PatternDisplay(props) {
  const {patterns, match: patternMatch, history} = props;
  var selectedPattern = patterns.filter(pattern => pattern.id === patternMatch.params.id).pop();
  var selectedSet = selectedPattern && selectedPattern.sets[patternMatch.params.set];

  if(!selectedPattern) {
    return <span></span>
  }

  const controls = (
    <ul className="tabs">
      <MenuLink to={`${patternMatch.url}/render/default`}>View</MenuLink>
      <MenuLink to={`${patternMatch.url}/source`}>Source</MenuLink>
      {/* Open in new window? */}
      {/* View HTML source? */}
    </ul>
  );

  const title = (
    <h4>{selectedPattern.name} <PatternSetIndicator selectedSet={patternMatch.params.set} sets={selectedPattern.sets} /></h4>
  );

  return (
    <AppFrame resizable={true} title={title} onClose={history.push.bind(history, '/')} controls={controls}>
      <Route path={`${patternMatch.url}/render/:set`} render={({match}) => <PatternRenderDisplay pattern={selectedPattern} set={match.params.set}/>}/>
      <Route path={`${patternMatch.url}/source`} render={() => <PatternSourceDisplay pattern={selectedPattern}/>}/>
      <Route path={patternMatch.url} exact render={() => <Redirect to={`${patternMatch.url}/render/default`}/>} />
    </AppFrame>
  )
}

const MenuLink = (props) => {
  return (
    <Route path={props.to} exact={true} children={({match}) => (
      <li className={match ? 'tabs-title is-active' : 'tabs-title'}>
        <Link aria-selected={match ? true : false} to={props.to}>{props.children}</Link>
      </li>
    )}/>
  )
}

const PatternSetIndicator = (props) => {
  const {sets, selectedSet} = props;
  const otherSets = Object.keys(sets).filter(setId => setId !== selectedSet);

  return (<span></span>)
//  return (
//    <div className="PatternSetIndicator">
//      <small>{sets[selectedSet].name}</small>
//      <ul className="PatternSetIndicator-nonselected">
//        {otherSets.map(setId => (
//          <li key={setId}><Link to={`${path}/render/${setId}`}>{sets[setId].name}</Link></li>
//        ))}
//      </ul>
//    </div>
//  )
}

const PatternSetSelection = (props) => {
  const {pattern, path} = props;
  return (

    <Route path={`${path}/render/:set`} children={({match}) => {
      if(!match) return [];
      const currSet = pattern.sets[match.params.set];
      const otherSets = Object.keys(pattern.sets).filter(setId => setId !== match.params.set);

      return(
        <span className="PatternSetSelection">
          <span className="PatternSetSelection-selected">{currSet.name}</span>
          {otherSets.length > 0 &&
            <span className="PatternSetSelection-others">
              {otherSets.map(setId => (
                <Link key={setId} to={`${path}/render/${setId}`}>{pattern.sets[setId].name}</Link>
              ))}
            </span>
          }
        </span>
      );
    }}/>
  )
}

const mapStateToProps = (state, ownProps) => {
  return {
    patterns: state.patterns,
    tags: state.tags
  }
}

export default connect(mapStateToProps)(PatternDisplay);