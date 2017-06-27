import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route,
  Redirect
} from 'react-router-dom';

import {connect} from 'react-redux';
import {fetchPatterns} from './actions';
import './App.css';

import NavBar from './NavBar';
import HomePage from './HomePage';
import TypePage from './TypePage';
import GroupPage from './GroupPage';
import PatternPage from './PatternPage';

class App extends Component {
  componentDidMount() {
    this.props.refreshPatterns();
  }
  render() {
    let {patterns, tags} = this.props;
    return (
      <Router>
        <div className="App">
          <NavBar patterns={patterns} tags={tags} />
          <Route path="/" exact component={HomePage} />
          <Route path="/pattern/:pattern" exact render={props => (
            <Redirect to={`${props.match.url}/set/default`} />
          )} />
          <Route path="/pattern/:pattern/set/:set" component={PatternPage} />
          <Route path="/type/:type" exact component={TypePage} />
          <Route path="/type/:type/group/:group" exact component={GroupPage} />
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

