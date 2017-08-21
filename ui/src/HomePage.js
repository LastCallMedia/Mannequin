
import React from 'react';
import './HomePage.css';
import {connect} from 'react-redux';
import Branding from './components/Branding';
import {getQuicklinks} from './selectors';
import Card from './components/Card';

const HomePage = ({quickLinks}) => (
    <main className="MannequinHome">
        <Branding/>
        {quickLinks.length > 0 &&
        <div className="quicklinks grid-container">
            <h4>Quick Links</h4>
            <div className="CardGrid">
                {quickLinks.map(pattern => (
                    <Card key={pattern.id} title={pattern.name} subtitle={pattern.metadata['group']} to={`pattern/${pattern.id}`} />
                ))}
            </div>
        </div>
        }
    </main>
)

const mapStateToProps = (state) => {
  return {
    quickLinks: getQuicklinks(state)
  }
}


export default connect(mapStateToProps)(HomePage)