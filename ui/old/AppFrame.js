
import React, {Component} from 'react';
import ReactResizeDetector from 'react-resize-detector';

class AppFrame extends Component{
  constructor(props, context) {
    super(props, context);
    this.handleKeypress = this.handleKeypress.bind(this);
    this.handleClose = this.handleClose.bind(this);
    this.handleResize = this.handleResize.bind(this);
    this.onResize = this.handleResize.bind(this);
    this.state = {width: 0, height: 0};
  }
  componentDidMount() {
    this.handleResize(this.modal.offsetWidth, this.modal.offsetHeight);
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
    const {children, controls, resizable, title} = this.props;
    return(
      <div className="reveal-overlay" style={{display: 'block'}}>
        <div className="AppFrame-nav">
          <button className="close-button" onClick={this.handleClose}><span aria-hidden="true">&times;</span></button>
          <div className="AppFrameNav-title">{title}</div>
          {controls && <div className="AppFrame-controls">{controls}</div>}
          {resizable && <span className="label secondary sizing">{`${this.state.height}x${this.state.width}`}</span>}
        </div>
        <div className={`AppFrame reveal${resizable ? ' resizable': ''}`} style={{display: 'block'}} ref={c => this.modal = c}>
          {children}
          {resizable && <ReactResizeDetector handleWidth handleHeight onResize={this.onResize} />}
        </div>
      </div>
    )
  }
}

export default AppFrame;