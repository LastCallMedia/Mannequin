import React, { Component } from 'react';
import { connect } from 'react-redux';
import ComponentTopBar from '../components/ComponentTopBar';
import ComponentInfo from '../components/ComponentInfo';
import RenderFrame from '../components/RenderFrame';
import ComponentProblems from '../components/ComponentProblems';
import Callout from '../components/Callout';
import TransitionGroup from 'react-transition-group/TransitionGroup';
import SampleSelector from '../components/SampleSelector';
import {
  OpenWindowButton,
  ViewInfoButton,
  CloseButton
} from '../components/Buttons/';
import Button from '../components/Buttons'
import { SlideInFromBottom } from '../components/Transitions';
import { toggleInfo, componentView } from '../actions';
import { getComponent, getSample, getUsed } from '../selectors';
import PropTypes from 'prop-types';
import './SamplePage.css';
import cx from 'classnames';


const SamplePage = ({ component, sample, ...rest }) => {
  if (!component) {
    return (
      <main>
        <Callout title={'Component not found'} type={'alert'} />
      </main>
    );
  }
  if (!sample) {
    return (
      <main>
        {component.problems.length > 0 && (
          <ComponentProblems problems={component.problems} />
        )}
        <Callout title={'Sample not found'} type={'alert'} />
      </main>
    );
  }
  return <SampleFoundPage component={component} sample={sample} {...rest} />;
};

SamplePage.propTypes = {
  component: PropTypes.shape({
    problems: PropTypes.arrayOf(PropTypes.string)
  }),
  sample: PropTypes.object
};

class SampleFoundPage extends Component {
  componentDidMount() {
    this.props.componentView(this.props.component);
  }
  componentDidUpdate(prevProps) {
    if (prevProps.component !== this.props.component) {
      this.props.componentView(this.props.component);
    }
  }
  render() {
    const {
      component,
      sample,
      showingInfo,
      toggleInfo,
      used,
      changeSample
    } = this.props;
    const { problems, name } = component;
    const actions = [
      <Button
        classes="Button NewWindowButton"
        icon="new-window"
        target="_blank"
        href={sample.rendered} />,
      <Button
        text="View Info"
        element="button"

        classes={cx({
          Button: true,
          ViewInfoButton: true,
          isToggled: showingInfo
        })}
        onClick={toggleInfo}
         />
    ];
    const selector = (
      <SampleSelector
        samples={component.samples}
        value={sample.id}
        onChange={changeSample}
      />
    );
    return (
      <main className="SampleFoundPage">
        <ComponentTopBar actions={actions} title={name} selector={selector} />
        {problems.length > 0 && (
          <ComponentProblems className="Content" problems={problems} />
        )}
        <RenderFrame src={sample.rendered} />
        <TransitionGroup>
          {showingInfo && (
            <SlideInFromBottom>
              <ComponentInfo
                component={component}
                sample={sample}
                used={used}
                controls={
                  <Button
                    text="Close"
                    element="button"
                    icon="close"
                    classes="Button CloseButton"
                    onClick={toggleInfo} />
                }
              />
            </SlideInFromBottom>
          )}
        </TransitionGroup>
      </main>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  return {
    component: getComponent(state, ownProps),
    sample: getSample(state, ownProps),
    showingInfo: state.info,
    used: getUsed(state, ownProps)
  };
};

const mapDispatchToProps = (dispatch, ownProps) => {
  return {
    toggleInfo: () => dispatch(toggleInfo()),
    componentView: component => dispatch(componentView(component)),
    changeSample: sid => {
      const cid = ownProps.match.params.component;
      ownProps.history.push(`/component/${cid}/sample/${sid}`);
    }
  };
};

export default connect(mapStateToProps, mapDispatchToProps)(SamplePage);
