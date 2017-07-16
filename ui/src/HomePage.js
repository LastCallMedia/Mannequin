
import React, {Component} from 'react';
import './HomePage.css';
import {createSelector} from 'reselect';
import {connect} from 'react-redux';
import logo from './svg/manny_wave.svg';
import {Link} from 'react-router-dom';

class HomePage extends Component {

  render() {
    const {quickLinks} = this.props;
    return (
      <main className="MannequinHome">
        <div className="branding">
          <img src={logo} alt="Mannequin" className="logo" />
          <h1>Mannequin <small>Pattern Library</small></h1>
        </div>
          {quickLinks.length > 0 &&
              <div className="quicklinks grid-container">
                  <h4>Quick Links</h4>
                  <div className="grid-x grid-padding-x align-center">
                      {quickLinks.map(quickLink => (
                          <PatternQuickLink key={quickLink.id} className="cell small-2" pattern={quickLink} />
                      ))}
                  </div>
              </div>
          }
      </main>
    )
  }
}

const PatternQuickLink = ({pattern, ...otherProps}) => (
    <QuickLinkCard name={pattern.name} category={pattern.tags['category'] || 'Unknown'} to={`/pattern/${pattern.id}`} {...otherProps} />
)

const QuickLinkCard = ({name, category, to, className}) => {
  return (
    <article className={`QuickLinkCard ${className}`}>
      <Link to={to}>
        <h6>{category}</h6>
        <h5>{name}</h5>
      </Link>
    </article>
  )
}

const getPatternsFromState = state => state.patterns;
const getQuickLinksFromState = state => state.quickLinks;
const getQuickLinks = createSelector(
  [getPatternsFromState, getQuickLinksFromState],
  (patterns, ids) => {
    var quickLinks = patterns
        .filter(pattern => -1 !== ids.indexOf(pattern.id))
        .sort((p1, p2) => ids.indexOf(p1.id) - ids.indexOf(p2.id));

    return quickLinks;
  }
)

const mapStateToProps = (state) => {
  return {
    quickLinks: getQuickLinks(state)
  }
}


export default connect(mapStateToProps)(HomePage)