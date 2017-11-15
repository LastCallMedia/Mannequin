
import React, {Component} from 'react';
import Link from 'gatsby-link';
import PropTypes from 'prop-types';
import cx from 'classnames'
import './SectionNav.scss';

export default class SectionNav extends Component {
  constructor(props) {
    super(props)
    this.state = {showing: false}
    this.toggleShowing = this.toggleShowing.bind(this)
  }
  toggleShowing() {
    this.setState(state => ({showing: !state.showing}))
  }
  render() {
    const {sections} = this.props
    const {showing} = this.state
    const currentSection = sections.filter(s => s.active).pop();
    return (
      <div className="SectionNav">
        {currentSection && <h3 className="current">{currentSection.title}</h3>}
        <label className="label" onClick={this.toggleShowing}>Change Extension</label>
        <ul className={cx({showing})}>
          {sections.map(section => (
            <li key={section.to}>
              <Link to={section.to}>
                {section.title}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    )
  }
}

SectionNav.propTypes = {
  sections: PropTypes.arrayOf(PropTypes.shape({
    title: PropTypes.string.isRequired,
    to: PropTypes.string.isRequired,
    active: PropTypes.bool,
  }))
}