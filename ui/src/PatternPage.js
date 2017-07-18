
import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';

import {patternView} from './actions';
import {getPattern, getVariant, getUsed} from './selectors';
import {PatternShape, VariantShape, UsedShape} from './types';

import {loadingWrapper} from './common';

import PatternTopBar from './components/PatternTopBar';
import PatternInfo from './components/PatternInfo';

import './PatternPage.css';


class PatternPage extends Component {
  constructor(props) {
    super(props)
    this.state = {showingInfo: false}
    this.toggleInfo = this.toggleInfo.bind(this)
    this.openWindow = this.openWindow.bind(this)
  }
  componentDidMount() {
    this.props.onPatternView(this.props.pattern)
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

    return (
      <main className="PatternPage">
        <PatternTopBar pattern={pattern} variant={variant} openWindow={this.openWindow} toggleInfo={this.toggleInfo} changeVariant={onVariantChange} />
        <div className="RenderFrame">
          <iframe title="Rendered Pattern" frameBorder="0" src={variant.rendered}></iframe>
        </div>

        <PatternInfo toggleInfo={this.toggleInfo} used={used} pattern={pattern} variant={variant} className={showingInfo ? 'open' : 'closed'} />
      </main>
    );
  }
}

PatternPage.propTypes = {
    pattern: PropTypes.shape(PatternShape),
    variant: PropTypes.shape(VariantShape),
    used: PropTypes.arrayOf(PropTypes.shape(UsedShape)),
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
export default connect(mapStateToProps, mapDispatchToProps)(loadingWrapper('pattern', PatternPage));

