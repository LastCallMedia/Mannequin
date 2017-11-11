import React, {Component} from 'react'
import PropTypes from 'prop-types';
import Branding from './Branding';
import MenuTree from './MenuTree';
import HamburgerButton from './HamburgerButton';
import Helmet from 'react-helmet'
import cx from 'classnames';
import './Page.scss';

export default class Page extends Component {
    constructor(props) {
        super(props)
        this.state = {showNav: false}
        this.toggleNav = this.toggleNav.bind(this)
    }
    toggleNav() {
        this.setState(state => ({showNav: !state.showNav}))
    }
    render() {
        const {title, description, menu, children} = this.props
        const {showNav} = this.state
        return (
            <div className="Page">
                <Helmet>
                    <title>{title} | Mannequin</title>
                    <meta name="description" content={description} />
                </Helmet>

                <header className="branding-wrap">
                    <div className="h-inner">
                        <Branding tiny to={'/'} />
                        <HamburgerButton onClick={this.toggleNav} />
                        <nav className={cx({'open': showNav})}>
                            <MenuTree links={menu} />
                        </nav>
                    </div>
                </header>
                <main>
                    <h1 className="title">{title}</h1>
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
    below: PropTypes.array.isRequired
})
Page.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.string,
  menu: PropTypes.arrayOf(menuItemShape),
  children: PropTypes.node.isRequired,
}
