import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route
} from 'react-router-dom';

import {connect} from 'react-redux';
import {fetchPatterns, toggleDrawer} from './actions';
import './App.css';
import 'what-input';

import TopBar from './components/TopBar';
import NavDrawer from './components/NavDrawer';
import HomePage from './HomePage';
import PatternPage from './PatternPage';
import VariantPage from './VariantPage';
import PropTypes from 'prop-types';

export class App extends Component {
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
              <Route path={'/pattern/:pattern'} exact component={PatternPage} />
              <Route path={'/pattern/:pattern/variant/:vid'} component={VariantPage} />
            </div>
            <NavDrawer patterns={patterns} open={drawer} toggleNav={toggleDrawer} />
          </div>
        </div>
      </Router>
    );
  }
}
App.propTypes = {
  patterns: PropTypes.arrayOf(PropTypes.object),
  refreshPatterns: PropTypes.func.isRequired,
  toggleDrawer: PropTypes.func.isRequired
}
App.defaultProps = {
  patterns: [],
  refreshPatterns: () => {},
  toggleDrawer: () => {},
}

const mapStateToProps = (state) => {
  return {
    patterns: state.patterns,
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

