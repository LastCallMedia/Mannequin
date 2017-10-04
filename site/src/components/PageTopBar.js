import React from 'react'
import Branding from './Branding'
import Link from 'gatsby-link';
import cx from 'classnames';
import './PageTopBar.scss';

export default class PageTopBar extends React.Component {
  constructor(props) {
    super(props)
    this.state = {open: false}
    this.toggleMenu = this.toggleMenu.bind(this);
  }
  toggleMenu() {
    this.setState(state => ({
        open: !state.open
    }))
  }
  render() {
    const {open} = this.state;
    return (
        <header className="TopBar">
          <div className="inner">
            <Link to="/">
              <Branding tiny dark />
            </Link>
            <div className="menu-toggle">
              <a role="button" aria-label="Open Menu" onClick={this.toggleMenu}>
                  {open ? <span className="close">&times;</span> : <i className="burger"></i>}
              </a>
            </div>
            <MainMenu className="for-large" />
          </div>
          <MainMenu className={`for-small${open ? ' open' : ' closed'}`} />
        </header>
    )
  }
}

function MainMenu({className}) {
  return (
      <ul className={cx('MainMenu', className)}>
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
  )
}
