
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {createSelector} from 'reselect';
import {Link} from 'react-router-dom';
import Highlight from 'react-syntax-highlight';
import './PatternPage.css';
import 'highlight.js/styles/default.css';
import 'highlight.js/styles/atom-one-dark.css';


const PatternPageLoadingWrapper = ({pattern, set, used, onSetChange}) => (
  <main className="PatternPageLoadingWrapper">
    {pattern && <PatternPage pattern={pattern} set={set} used={used} onSetChange={onSetChange} />}
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
const mapDispatchToProps = (state, ownProps) => {
  return {
    onSetChange: (sid) => {
      ownProps.history.push(`/pattern/${ownProps.match.params.pattern}/set/${sid}`)
    }
  }
}
export default connect(mapStateToProps, mapDispatchToProps)(PatternPageLoadingWrapper);


class PatternPage extends Component {
  constructor(props) {
    super(props)
    this.state = {showingInfo: false}
    this.toggleInfo = this.toggleInfo.bind(this)
    this.openWindow = this.openWindow.bind(this)
  }
  toggleInfo(e) {
    this.setState(state => ({
      showingInfo: !state.showingInfo
    }))
  }
  openWindow() {
    window.open(this.props.set.rendered, this.props.pattern.name, 'resizable');
  }
  render() {
    const {pattern, set, used, onSetChange} = this.props;
    const {showingInfo} = this.state;
    return (
      <main className="PatternPage">
        <div className="top-bar">
          <div className="top-bar-left">
            <ul className="menu horizontal">
              <li className="menu-text">{pattern.name}</li>
              <li><SetSelector sets={pattern.sets} selected={set.id} onChange={onSetChange} /></li>
            </ul>
          </div>
          <div className="top-bar-right">
            <ul className="menu">
              <li><a onClick={this.openWindow}>Open</a></li>
              <li><a onClick={this.toggleInfo} className="button">View Pattern Info</a></li>
            </ul>
          </div>
        </div>
        <div className="RenderFrame">
          <iframe frameBorder="0" src={set.rendered}></iframe>
        </div>
        <div className={`PatternInfo grid-container ${showingInfo ? 'open' : 'closed'}`}>
          <div className="grid-x grid-padding-x">
            <button onClick={this.toggleInfo} className="close-button" aria-label="Open Info" type="button"><span aria-hidden="true">&times;</span></button>
            <div className="cell medium-auto">
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
            <CodeToggleFrame className="cell medium-auto" html={set.source} raw={pattern.source} rawFormat={pattern.format} />
          </div>
        </div>
      </main>
    );
  }
}

class CodeToggleFrame extends Component {
  constructor(props) {
    super(props);
    this.state = {mode: 'html'}
    this.switchMode = this.switchMode.bind(this);
  }
  switchMode(e) {
    this.setState({mode: e.target.getAttribute('data-mode')})
  }
  render() {
    const {className} = this.props;
    const {mode} = this.state;
    const src = this.props[mode];
    const title = mode === 'html' ?  'View Html' : 'View Raw';
    const format = mode === 'html' ? 'html' : this.props.rawFormat;

    return (
      <div className={`CodeFrame ${className}`}>
        <CodeFrame frameBorder="0" title={title} src={src} format={format}></CodeFrame>
        <div className="button-group">
          <a className="button" data-mode="html" onClick={this.switchMode}>HTML</a>
          <a className="button" data-mode="raw" onClick={this.switchMode}>Raw</a>
        </div>
      </div>
    )
  }
}

class CodeFrame extends Component {
  constructor(props) {
    super(props)
    this.state = {code: ''}
  }
  componentDidMount() {
    this.fetch();
  }
  componentDidUpdate(prevProps) {
    if(prevProps.src !== this.props.src) {
      this.fetch();
    }
  }
  fetch() {
    this.setState({loading: true, err: false});
    fetch(this.props.src)
      .then(res => {
        this.setState({loading: false});
        res.text().then(code => this.setState({code}));
      })
      .catch(err => {
        this.setState({loading: false, err: err})
      })
  }
  render() {
    const {code, loading, err} = this.state;
    const {format} = this.props;

    if(loading) {
      return <p>Loading...</p>
    }
    if(err) {
      return <p>Error: {err}</p>
    }
    return <Highlight lang={format} value={code} />
  }
}

const SetSelector = ({sets, selected, onChange}) => (
  <select className="SetSelector" value={selected} onChange={e => onChange(e.target.value)}>
    {sets.map(set => (
      <option key={set.id} value={set.id}>{set.name}</option>
    ))}
  </select>
)