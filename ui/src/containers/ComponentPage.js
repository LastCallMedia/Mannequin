import React from 'react';
import { Redirect } from 'react-router-dom';
import { connect } from 'react-redux';
import { getComponent } from '../selectors';
import Callout from '../components/Callout';
import ComponentProblems from '../components/ComponentProblems';
import PropTypes from 'prop-types';

const ComponentPage = ({ component }) => {
  if (!component) {
    return (
      <main>
        <Callout title={'Component not found'} type={'alert'} />
      </main>
    );
  }
  if (!component.samples.length) {
    return (
      <main>
        {component.problems.length > 0 && (
          <ComponentProblems problems={component.problems} />
        )}
        <Callout title={'No samples found.'} type={'alert'} />
      </main>
    );
  }
  return (
    <Redirect
      to={`/component/${component.id}/sample/${component.samples[0].id}`}
    />
  );
};

ComponentPage.propType = {
  component: PropTypes.shape({
    id: PropTypes.string.isRequired,
    samples: PropTypes.arrayOf(
      PropTypes.shape({
        id: PropTypes.string.isRequired
      })
    )
  })
};

const mapStateToProps = (state, ownProps) => {
  return {
    component: getComponent(state, ownProps)
  };
};

export default connect(mapStateToProps)(ComponentPage);
