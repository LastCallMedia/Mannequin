import React, { Component } from 'react'
import Branding from './Branding'
import './PageTopBar.scss'

export default function PageTopBar() {
  return (
    <header className="TopBar">
      <div className="inner">
        <div className="branding-wrap">
          <Branding to="/" tiny />
        </div>
      </div>
    </header>
  )
}
