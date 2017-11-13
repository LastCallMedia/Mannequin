import React from 'react'
import LcmLogo from '../img/lcm-logo.png'
import './HomeTopBar.scss'

export default function HomeTopBar() {
  return (
    <div className="HomepageTopBar">
      <a
        className="logo"
        href="https://lastcallmedia.com"
        title="Visit Last Call Media"
      >
        <img src={LcmLogo} alt="Last Call Media Logo" />
      </a>
      <a className="maintained" href="https://lastcallmedia.com">
        Maintained by Last Call Media
      </a>
    </div>
  )
}
