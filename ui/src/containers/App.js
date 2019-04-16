import React, { Component } from 'react';
import { HashRouter as Router, Route, withRouter } from 'react-router-dom';

import { connect } from 'react-redux';
import { fetchComponents, toggleDrawer } from '../actions';
// import './App.css';
import 'what-input';

import TopBar from '../components/TopBar';
import NavDrawer from '../components/NavDrawer';
import HomePage from '../containers/HomePage';
import ComponentPage from '../containers/ComponentPage';
import SamplePage from '../containers/SamplePage';
import PropTypes from 'prop-types';

export class App extends Component {
  componentDidMount() {
    this.props.refreshComponents();
  }
  render() {
    let { components, drawer, toggleDrawer } = this.props;
    return (
      <Router>
        <div className={`App ${drawer ? 'drawer-open' : 'drawer-closed'}`}>
          <div className="app-inner">
            <div className="main-frame">
              <TopBar toggleNav={toggleDrawer} />
              <Route path="/" exact component={HomePage} />
              <Route
                path={'/component/:component'}
                exact
                component={ComponentPage}
              />
              <Route
                path={'/component/:component/sample/:sid'}
                component={SamplePage}
              />
            </div>
            <NavDrawer
              components={components}
              open={drawer}
              toggleNav={toggleDrawer}
            />
          </div>
          {drawer && <DrawerSubscriber action={toggleDrawer} />}
        </div>
      </Router>
    );
  }
}
App.propTypes = {
  components: PropTypes.arrayOf(PropTypes.object),
  refreshComponents: PropTypes.func.isRequired,
  toggleDrawer: PropTypes.func.isRequired
};
App.defaultProps = {
  components: [],
  refreshComponents: () => {},
  toggleDrawer: () => {}
};

const DrawerSubscriber = withRouter(
  class extends Component {
    componentDidUpdate(prevProps) {
      if (prevProps.location !== this.props.location) {
        this.props.action();
      }
    }
    render() {
      return this.props.children || null;
    }
  }
);

const mapStateToProps = state => {
  return {
    components: state.components,
    drawer: state.drawer
  };
};

const mapDispatchToProps = dispatch => {
  return {
    refreshComponents: () => {
      dispatch(fetchComponents());
    },
    toggleDrawer: () => {
      dispatch(toggleDrawer());
    }
  };
};

export default connect(mapStateToProps, mapDispatchToProps)(App);
