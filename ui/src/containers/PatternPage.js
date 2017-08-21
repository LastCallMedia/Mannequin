
import React from 'react';
import {Redirect} from 'react-router-dom';
import {connect} from 'react-redux'
import {getPattern} from '../selectors';
import Callout from '../components/Callout';
import PatternProblems from '../components/PatternProblems';

const PatternPage = ({pattern}) => {
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


const mapStateToProps = (state, ownProps) => {
    return {
        pattern: getPattern(state, ownProps),
    }
}

export default connect(mapStateToProps)(PatternPage);