
import React, {Component} from 'react';
import './HomePage.css';
import logo from './logo.svg';

class HomePage extends Component {

  render() {
    return (
      <main className="MannequinHome">
        <div className="branding">
          <img src={logo} className="logo" />
          <h1>Mannequin <small>Pattern Library</small></h1>
        </div>
        <div className="quicklinks">
          <h4>Quick Links</h4>
          <div className="row align-center small-up-6">
            <QuickLinkCard className="column" name="Global" category="Organisms" />
            <QuickLinkCard className="column" name="Homepage" category="Pages" />
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

export default HomePage