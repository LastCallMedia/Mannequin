import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route
} from 'react-router-dom';

import PatternBoard from './PatternBoard';
import AppFrame from './AppFrame';
import {PatternRenderDisplay} from './Pattern';
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
          <Route path="/pattern/:id" render={(props) => {
            var selectedPattern = patterns.filter(pattern => pattern.id === props.match.params.id).pop();
            if(!selectedPattern) {
              return (<AppFrame><h1>Pattern not found</h1></AppFrame>)
            }
            return (
              <AppFrame onClose={props.history.goBack}>
                <PatternRenderDisplay pattern={selectedPattern}/>
              </AppFrame>
            );
          }}/>
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
