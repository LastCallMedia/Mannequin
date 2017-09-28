import React from 'react'
import Branding from './Branding'
import Link from 'gatsby-link';
import './PageTopBar.scss';

export default function PageTopBar() {
  return (
    <header className="TopBar">
      <div className="inner">
        <Link to="/">
          <Branding tiny dark />
        </Link>
        <ul className="menu">
          <li>
            <Link to="docs">Documentation</Link>
          </li>
          <li>
            <Link to="extensions">Extensions</Link>
          </li>
          <li>
            <Link to="about">About</Link>
          </li>
        </ul>
      </div>
    </header>
  )
}
