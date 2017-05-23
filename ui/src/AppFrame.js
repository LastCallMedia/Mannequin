
import React, {Component} from 'react';
import ReactResizeDetector from 'react-resize-detector';

import './AppFrame.css';

class AppFrame extends Component{
  constructor(props, context) {
    super(props, context);
    this.handleKeypress = this.handleKeypress.bind(this);
    this.handleClose = this.handleClose.bind(this);
    this.handleResize = this.handleResize.bind(this);
    this.state = {width: 0, height: 0};
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
      default:
        break;
    }
  }
  handleClose() {
    this.props.onClose();
  }
  handleResize(width, height) {
    this.setState({
      width,
      height
    });
  }
  render() {
    const {children, controls} = this.props;
    return(
      <div className="AppFrame">
        <div className="AppFrame-controls">
          <a onClick={this.handleClose} className="AppFrame-close">x</a>
          <span className="size">{`${this.state.width}x${this.state.height}`}</span>
          {controls}
        </div>
        <ResizableFrame onResize={this.handleResize}>{children}</ResizableFrame>
      </div>
    )
  }
}

class ResizableFrame extends Component {
  constructor() {
    super();
    this.onResize = this.onResize.bind(this);
  }
  componentDidMount() {
    // Get the initial size.
    this.onResize(this.resizable.offsetWidth, this.resizable.offsetHeight);
  }
  onResize(w, h) {
    if(this.props.onResize) {
      this.props.onResize(w, h);
    }
  }
  render() {
    return (
      <div className="ResizableFrame" ref={c => this.resizable = c}>
        {this.props.children}
        <ReactResizeDetector handleWidth handleHeight onResize={this.onResize} />
      </div>
    )
  }
}

export default AppFrame;