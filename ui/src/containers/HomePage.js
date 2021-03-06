import React from 'react';
import './HomePage.css';
import { connect } from 'react-redux';
import Branding from '../components/Branding';
import { getQuicklinks } from '../selectors';
import Card from '../components/Card';
import PropTypes from 'prop-types';

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
    )}
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
