
import React, {Component} from 'react';
import {Redirect} from 'react-router-dom';
import {connect} from 'react-redux'
import {getPattern} from './selectors';
import {toggleInfo, patternView} from './actions';
import Callout from './components/Callout';
import PatternProblems from './components/PatternProblems';

class PatternPage extends Component {
    render() {
        const {pattern} = this.props;

        if(!pattern) {
            return (
                <main>
                    <Callout title={"Pattern not found"} type={'alert'} />
                </main>
            )
        }
        if(!pattern.variants.length) {
            return (
                <main>
                    {pattern.problems.length > 0 && <PatternProblems problems={pattern.problems}/>}
                    <Callout title={"No variants found."} type={'alert'} />
                </main>
            )
        }
        return <Redirect to={`/pattern/${pattern.id}/variant/${pattern.variants[0].id}`}/>
    }
}


const mapStateToProps = (state, ownProps) => {
    return {
        pattern: getPattern(state, ownProps),
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        toggleInfo: () => dispatch(toggleInfo()),
        patternView: (pattern) => dispatch(patternView(pattern))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PatternPage);