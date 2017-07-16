
import React, {Component} from 'react';
import './HomePage.css';
import {createSelector} from 'reselect';
import {connect} from 'react-redux';
import logo from './svg/manny_wave.svg';

class HomePage extends Component {

  render() {
    return (
      <main className="MannequinHome">
        <div className="branding">
          <img src={logo} alt="Mannequin" className="logo" />
          <h1>Mannequin <small>Pattern Library</small></h1>
        </div>
        <div className="quicklinks grid-container">
          <h4>Quick Links</h4>
          <div className="grid-x grid-padding-x align-center">
            <QuickLinkCard className="cell small-2" name="Global" category="Organisms" />
            <QuickLinkCard className="cell small-2" name="Homepage" category="Pages" />
          </div>
        </div>
      </main>
    )
  }
}

const QuickLinkCard = ({name, category, path, className}) => {
  return (
    <article className={`QuickLinkCard ${className}`}>
      <a>
        <h6>{category}</h6>
        <h5>{name}</h5>
      </a>
    </article>
  )
}

const getPatternsFromState = state => state.patterns;
const getQuickLinksFromState = state => state.quickLinks;
const getQuickLinks = createSelector(
  [getPatternsFromState, getQuickLinksFromState],
  (patterns, quickLinks) => {
    console.log(quickLinks);
  }
)

const mapStateToProps = (state) => {
  return {
    quickLinks: getQuickLinks(state)
  }
}


export default connect(mapStateToProps)(HomePage)