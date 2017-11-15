import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Branding from './Branding'
import PageNav from './PageNav'
import HamburgerButton from './HamburgerButton'
import PencilIcon from 'react-icons/lib/go/pencil'
import Helmet from 'react-helmet'
import cx from 'classnames'
import './Page.scss'

export default class Page extends Component {
  constructor(props) {
    super(props)
    this.state = { showNav: false }
    this.toggleNav = this.toggleNav.bind(this)
  }
  toggleNav() {
    this.setState(state => ({ showNav: !state.showNav }))
  }
  render() {
    const { title, description, menu, section, edit, children } = this.props
    const { showNav } = this.state
    return (
      <div className="Page">
        <Helmet>
          <title>{title} | Mannequin</title>
          <meta name="description" content={description} />
        </Helmet>

        <header>
          <Branding tiny to={'/'} />
          <HamburgerButton onClick={this.toggleNav} />
          <PageNav
            className={cx({ open: showNav })}
            section={section}
            menu={menu}
          />
        </header>
        <main>
          <h1 className="title">{title}</h1>
          {edit && (
            <a title="Suggest an edit" className="EditLink" href={edit}>
              <PencilIcon />
            </a>
          )}
          <div className="content">{children}</div>
        </main>
      </div>
    )
  }
}

const menuItemShape = PropTypes.shape({
  to: PropTypes.string.isRequired,
  title: PropTypes.node.isRequired,
  active: PropTypes.bool.isRequired,
  below: PropTypes.array.isRequired,
})
Page.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.string,
  edit: PropTypes.string,
  menu: PropTypes.arrayOf(menuItemShape),
  section: PropTypes.string,
  children: PropTypes.node.isRequired,
}
Page.defaultProps = {
  menu: [],
}
