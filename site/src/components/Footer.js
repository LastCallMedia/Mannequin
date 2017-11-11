import React from 'react'
import Branding from './Branding'
import './Footer.scss';

export default function Footer() {
  return (
    <footer className="page-footer">
      <Branding />
      <a
        className="footer-link button dashing-icon"
        href="https://github.com/LastCallMedia/Mannequin/issues"
      >
        <i className="icon icon-github" />
        <span className="text">Get Support on Github</span>
      </a>
    </footer>
  )
}
