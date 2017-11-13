import React, { Component } from 'react'
import { withRouter } from 'react-router-dom'

export default withRouter(
  class extends Component {
    componentDidUpdate(prevProps) {
      if (prevProps.location !== this.props.location) {
        this.props.action()
      }
    }
    render() {
      return this.props.children || null
    }
  }
)
