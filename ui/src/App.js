import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route
} from 'react-router-dom';

import {connect} from 'react-redux';
import {fetchPatterns} from './actions';
import './App.css';

import MannequinHome from './MannequinHome';
import MannequinNav from './MannequinNav';


class App extends Component {
  componentDidMount() {
    this.props.refreshPatterns();
  }
  render() {
    let {patterns, tags} = this.props;
    return (
      <Router>
        <div className="App">
          <MannequinNav patterns={patterns} tags={tags} />
          <Route path="/" exact component={MannequinHome} />
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

