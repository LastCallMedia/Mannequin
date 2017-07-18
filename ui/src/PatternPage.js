
import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';

import {patternView} from './actions';
import {Link} from 'react-router-dom';
import Highlight from 'react-syntax-highlight';
import {OpenNew} from './Icon';
import {getPattern, getVariant, getUsed} from './selectors';
import './PatternPage.css';
import 'highlight.js/styles/default.css';
import 'highlight.js/styles/atom-one-dark.css';

const VariantShape = {
  name: PropTypes.string,
  description: PropTypes.string,
  rendered: PropTypes.string.isRequired
}
const PatternShape = {
  name: PropTypes.string.isRequired,
  description: PropTypes.string,
  rendered: PropTypes.string,
  used: PropTypes.arrayOf(PropTypes.string),
  variants: PropTypes.arrayOf(PropTypes.shape(VariantShape)),
  tags: PropTypes.shape(),
};


const PatternPageLoadingWrapper = ({pattern, variant, used, onVariantChange, onPatternView}) => (
  <main className="PatternPageLoadingWrapper">
    {pattern && <PatternPage pattern={pattern} variant={variant} used={used} onVariantChange={onVariantChange} onPatternViewed={onPatternView} />}
  </main>
)
PatternPageLoadingWrapper.propTypes = {
  pattern: PropTypes.shape(PatternShape),
  variant: PropTypes.shape(VariantShape),
  used: PropTypes.arrayOf(PropTypes.shape({
    name: PropTypes.string.isRequired,
    id: PropTypes.string.isRequired
  })),
  onVariantChange: PropTypes.func.isRequired,
  onPatternView: PropTypes.func.isRequired
}

const mapStateToProps = (state, ownProps) => {
  return {
    pattern: getPattern(state, ownProps),
    variant: getVariant(state, ownProps),
    used: getUsed(state, ownProps)
  }
}
const mapDispatchToProps = (dispatch, ownProps) => {
  return {
    onVariantChange: (vid) => {
      ownProps.history.push(`/pattern/${ownProps.match.params.pattern}/variant/${vid}`)
    },
    onPatternView: (pattern) => {
      dispatch(patternView(pattern));
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
  componentDidMount() {
    this.props.onPatternViewed(this.props.pattern)
  }
  toggleInfo(e) {
    this.setState(state => ({
      showingInfo: !state.showingInfo
    }));
    e.preventDefault();
  }
  openWindow(e) {
    window.open(this.props.variant.rendered, this.props.pattern.name, 'resizable');
  }
  render() {
    const {pattern, variant, used, onVariantChange} = this.props;
    const {showingInfo} = this.state;
    const rawFormat = pattern.tags.source_format || 'html';
    return (
      <main className="PatternPage">
        <PatternTopBar pattern={pattern} variant={variant} openWindow={this.openWindow} toggleInfo={this.toggleInfo} changeVariant={onVariantChange} />
        <div className="RenderFrame">
          <iframe title="Rendered Pattern" frameBorder="0" src={variant.rendered}></iframe>
        </div>

        <div className={`PatternInfo ${showingInfo ? 'open' : 'closed'}`}>
          <div className="inner">
            <button onClick={this.toggleInfo} className="close-button" aria-label="Open Info" type="button"><span aria-hidden="true">&times;</span></button>
            <div className="info">
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
            <CodeToggleFrame className="code" html={variant.source} raw={pattern.source} rawFormat={rawFormat} />
          </div>
        </div>
      </main>
    );
  }
}

const PatternTopBar = ({pattern, variant, openWindow, toggleInfo, changeVariant}) => {
  return (
    <div className="PatternTopBar">
      <div className="inner">
        <h4 className="name">{pattern.name}</h4>
        <div className="variant"><VariantSelector variants={pattern.variants} selected={variant.id} onChange={changeVariant} /></div>
        <ul className="actions">
          <li><button onClick={toggleInfo} className="PatternInfoButton">View Pattern Info</button></li>
          <li><a onClick={openWindow} target="_blank" className="OpenWindowButton" href={variant.rendered}><OpenNew /></a></li>
        </ul>
      </div>
    </div>
  )
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

const VariantSelector = ({variants, selected, onChange}) => (
  <select className="VariantSelector" value={selected} onChange={e => onChange(e.target.value)}>
    {variants.map(variant => (
      <option key={variant.id} value={variant.id}>{variant.name}</option>
    ))}
  </select>
)