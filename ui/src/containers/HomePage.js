import React from 'react';
import './HomePage.css';
import { connect } from 'react-redux';
import Branding from '../components/Branding';
import { getQuicklinks } from '../selectors';
import Card from '../components/Card';
import PropTypes from 'prop-types';
import { OpenNew } from '../components/Icons';
import Button from '../components/Buttons/Button';

const HomePage = ({ quickLinks }) => (
  <main className="MannequinHome">
    <Branding />
    {quickLinks.length > 0 && (
      <div className="quicklinks">
        <h4>Quick Links</h4>
        <div className="CardGrid">
          {quickLinks.map(component => (
            <Card
              key={component.id}
              title={component.name}
              subtitle={component.metadata['group']}
              to={`component/${component.id}`}
            />
          ))}
        </div>
      </div>
    ) || <Button
            text="Get Started"
            href="https://mannequin.io/#GetStarted"
            icon="new-window"
            classes="Button GetStartedButton"
            target="_blank" />}
  </main>
);

HomePage.propTypes = {
  quickLinks: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string.isRequired,
      name: PropTypes.string.isRequired,
      metadata: PropTypes.isObject
    })
  )
};

const mapStateToProps = state => {
  return {
    quickLinks: getQuicklinks(state)
  };
};

export default connect(mapStateToProps)(HomePage);
