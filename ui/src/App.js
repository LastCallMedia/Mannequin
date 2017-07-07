import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route,
  Redirect
} from 'react-router-dom';

import {connect} from 'react-redux';
import {fetchPatterns, toggleDrawer} from './actions';
import './App.css';

import {TopBar, NavDrawer} from './NavBar';
import HomePage from './HomePage';
import TypePage from './TypePage';
import GroupPage from './GroupPage';
import PatternPage from './PatternPage';

class App extends Component {
  componentDidMount() {
    this.props.refreshPatterns();
  }
  render() {
    let {patterns, drawer, toggleDrawer} = this.props;
    return (
      <Router>
        <div className={`App ${drawer ? 'drawer-open' : 'drawer-closed'}`}>
          <div className="app-inner">

            <div className="main-frame">
              <TopBar toggleNav={toggleDrawer} />
              <Route path="/" exact component={HomePage} />
              <Route path="/pattern/:pattern" exact render={props => (
                <Redirect to={`${props.match.url}/set/default`} />
              )} />
              <Route path="/pattern/:pattern/set/:set" component={PatternPage} />
              <Route path="/type/:type" exact component={TypePage} />
              <Route path="/type/:type/group/:group" exact component={GroupPage} />
            </div>
            <NavDrawer patterns={patterns} open={drawer} toggleNav={toggleDrawer} />
          </div>
        </div>
      </Router>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  return {
    patterns: state.patterns,
    tags: state.tags,
    drawer: state.drawer,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    refreshPatterns: () => {
      dispatch(fetchPatterns())
    },
    toggleDrawer: () => {
      dispatch(toggleDrawer())
    }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(App);

