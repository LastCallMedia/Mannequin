import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route
} from 'react-router-dom';

import PatternBoard from './PatternBoard';
import PatternView from './Pattern';
import {connect} from 'react-redux';
import {fetchPatterns} from './actions';
import './App.css';


class App extends Component {
  componentDidMount() {
    this.props.refreshPatterns();
  }
  render() {
    let {patterns, tags} = this.props;
    return (
      <Router>
        <div className="App">
          <PatternBoard patterns={patterns} tags={tags} grouping={'type'} />
          <Route path={'/pattern/:id/:set'} component={PatternView}/>
        </div>
      </Router>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  return {
    patterns: state.patterns,
    tags: state.tags
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    refreshPatterns: () => {
      dispatch(fetchPatterns())
    },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(App);
