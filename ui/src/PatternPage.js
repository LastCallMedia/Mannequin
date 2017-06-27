
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {createSelector} from 'reselect';
import {Link} from 'react-router-dom';
import './PatternPage.css';

const PatternPageLoadingWrapper = ({pattern, set, used}) => (
  <main className="PatternPageLoadingWrapper">
    {pattern && <PatternPage pattern={pattern} set={set} used={used} />}
  </main>
)
/**
 * These reselect selectors pull data out of redux state based on URL params.
 */
const getPatternsFromState = state => state.patterns;
const getSelectedPatternId = (state, ownProps) => ownProps.match.params.pattern;
const getSelectedSetId = (state, ownProps) => ownProps.match.params.set;
const getPattern = createSelector(
  [getPatternsFromState, getSelectedPatternId],
  (patterns, patternId) => {
    return patterns.filter(p => p.id === patternId).pop();
  }
)
const getSet = createSelector(
  [getPattern, getSelectedSetId],
  (pattern, setId) => {
    return pattern ? pattern.sets.filter(s => s.id === setId).pop() : undefined;
  }
)
const getUsed = createSelector(
  [getPatternsFromState, getPattern],
  (patterns, pattern) => {
    return pattern ? pattern.used.map(id => (
      patterns.filter(p => p.id === id).pop()
    )) : [];
  }
)

const mapStateToProps = (state, ownProps) => {
  return {
    pattern: getPattern(state, ownProps),
    set: getSet(state, ownProps),
    used: getUsed(state, ownProps)
  }
}
export default connect(mapStateToProps)(PatternPageLoadingWrapper);


class PatternPage extends Component {
  render() {
    const {pattern, set, used} = this.props;

    return (
      <main className="PatternPage">
        <div className="row">
          <div className="columns small-12">
            <h1 className="page-title">{pattern.name}</h1>
            {/* @todo: Set selection */}
          </div>
        </div>

        <div className="row columns">
          <iframe frameBorder="0" src={set.rendered}></iframe>
        </div>
        <div className="PatternInfo row">
          <div className="columns medium-6">
            <h3>{pattern.name}</h3>
            <div className="PatternInfoSection">
              <label>Uses</label>
              <p>{used.map(p => (
                <Link key={p.id} to={`/pattern/${p.id}`}>{p.name}</Link>
              ))}</p>
            </div>
            <div className="PatternInfoSection">
              <label>Description</label>
              <p>{pattern.description}</p>
            </div>
          </div>
          <CodeFrame className="columns medium-6" html={pattern.source} raw={pattern.source} />
        </div>
      </main>
    );
  }
}

class CodeFrame extends Component {
  constructor(props) {
    super(props);
    this.state = {mode: 'html'}
    this.switchMode = this.switchMode.bind(this);
  }
  switchMode(e) {
    this.setState({state: e.target.getAttribute('data-mode')})
  }
  render() {
    const {className} = this.props;
    const {mode} = this.state;
    const src = this.props[mode];
    const title = mode === 'html' ?  'View Html' : 'View Raw';

    return (
      <div className={`CodeFrame ${className}`}>
        <iframe frameBorder="0" title={title} src={src}></iframe>
        <div className="button-group">
          <a className="button" data-mode="html" onClick={this.switchMode}>HTML</a>
          <a className="button" data-mode="raw" onClick={this.switchMode}>Raw</a>
        </div>
      </div>
    )
  }
}