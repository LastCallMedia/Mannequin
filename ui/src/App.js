import React, { Component } from 'react';
import {
  HashRouter as Router,
  Route
} from 'react-router-dom';

import PatternBoard from './PatternBoard';
import {PatternRenderDisplay} from './Pattern';
import {connect} from 'react-redux';
import {fetchPatterns} from './actions';
import {Link} from 'react-router-dom';
import './App.css';
import Resizable from 'react-resizable-box';

class App extends Component {
  componentDidMount() {
    this.props.refreshPatterns();
  }
  render() {
    let {patterns, tags, refreshPatterns} = this.props;
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

class AppFrame extends Component{
  constructor(props, state, context) {
    super(props, state, context);
    this.handleKeypress = this.handleKeypress.bind(this);
    this.handleClose = this.handleClose.bind(this);
  }
  componentWillMount() {
    document.addEventListener('keydown', this.handleKeypress, false);
  }
  componentWillUnmount() {
    document.removeEventListener('keydown', this.handleKeypress, false);
  }
  handleKeypress(event) {
    switch(event.keyCode) {
      case 27: // Escape key:
        this.handleClose();
        break;
    }
  }
  handleClose() {
    this.props.onClose();
  }
  render() {
    const {children} = this.props;
    return(
      <div className="AppFrame">
        <a onClick={this.handleClose} className="AppFrame-close">x</a>
        <Resizable width={900} height={600} className="AppFrame-inner">
          {children}
        </Resizable>
      </div>
    )
  }
}

//function AppFrame(props) {
//  return(
//    <div className="AppFrame">
//      <Link to="/" className="AppFrame-close">x</Link>
//      <Resizable width={320} height={200} className="AppFrame-inner">
//        {props.children}
//      </Resizable>
//    </div>
//  )
//}

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
