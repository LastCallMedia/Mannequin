
import React, {Component} from 'react';
import {connect} from 'react-redux'
import PatternTopBar from './components/PatternTopBar';
import PatternInfo from './components/PatternInfo';
import RenderFrame from './components/RenderFrame';
import PatternProblems from './components/PatternProblems';
import Callout from './components/Callout';
import TransitionGroup from 'react-transition-group/TransitionGroup';
import {OpenWindowButton, ViewInfoButton, CloseButton} from './components/Buttons/';
import {SlideInFromBottom} from './components/Transitions';
import {toggleInfo, patternView} from './actions';
import {getPattern, getVariant, getUsed} from './selectors';
import './VariantPage.css'

class VariantPage extends Component {
    render() {
        const {pattern, variant, ...rest} = this.props;
        if(!pattern) {
            return (
                <main>
                    <Callout title={'Pattern not found'} type={'alert'} />
                </main>
            )
        }
        if(!variant) {
            return (
                <main>
                    {pattern.problems.length > 0 && <PatternProblems problems={pattern.problems} />}
                    <Callout title={'Variant not found'} type={'alert'} />
                </main>
            )
        }
        return <VariantFoundPage pattern={pattern} variant={variant} {...rest} />
    }
}

const VariantFoundPage = ({pattern, variant, showingInfo, toggleInfo}) => {
    const {problems, name} = pattern;
    const actions = [
        <OpenWindowButton href={variant.rendered} />,
        <ViewInfoButton onClick={toggleInfo} />
    ];
    return (
        <main className="VariantFoundPage">
            <PatternTopBar actions={actions} title={name} />
            {problems.length > 0 && <PatternProblems className="Content" problems={problems} />}
            <RenderFrame src={variant.rendered} />
            <TransitionGroup>
                {showingInfo &&
                    <SlideInFromBottom>
                        <PatternInfo pattern={pattern} variant={variant} controls={<CloseButton onClick={toggleInfo} />} />
                    </SlideInFromBottom>
                }
            </TransitionGroup>
        </main>
    )
}

const mapStateToProps = (state, ownProps) => {
    return {
        pattern: getPattern(state, ownProps),
        variant: getVariant(state, ownProps),
        showingInfo: state.info,
        used: getUsed(state, ownProps)
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        toggleInfo: () => dispatch(toggleInfo()),
        patternView: (pattern) => dispatch(patternView(pattern))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(VariantPage);