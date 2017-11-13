import React, { Component } from 'react'
import './MouseIcon.scss'

export default class MouseIcon extends Component {
  constructor(props) {
    super(props)
    this.state = { hasScrolled: false }
    // Bind the handleScroll event to this object, otherwise it can become
    // dereferenced.
    this.handleScroll = this.handleScroll.bind(this)
  }
  // Handle any scroll event
  handleScroll(e) {
    this.setState({ hasScrolled: window.scrollY > 0 })
  }
  // Lifecycle callback to attach the event listener when the component is mounted.
  componentDidMount() {
    window.addEventListener('scroll', this.handleScroll)
  }
  // Lifecycle callback to detach the event listener when the component is unmounted.
  componentWillUnmount() {
    window.removeEventListener('scroll', this.handleScroll)
  }
  render() {
    return (
      <div className={`mouse-icon${this.state.hasScrolled ? ' fade-out' : ''}`}>
        <div className="wheel" />
      </div>
    )
  }
}
