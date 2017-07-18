
import React from 'react';
import {Redirect} from 'react-router-dom';
import {connect} from 'react-redux'
import {getPattern} from './selectors'


const PatternRedirectPage = ({pattern}) => {
    if(pattern) {
        const vid = pattern.variants[0].id;
        return <Redirect to={`/pattern/${pattern.id}/variant/${vid}`} />
    }
    // @todo: Replace with a useful message about why your poor life decisions
    // landed you here.
    return <span>Nothing here...</span>
}

const mapStateToProps = (state, ownProps) => {
    return {
        pattern: getPattern(state, ownProps)
    }
}

export default connect(mapStateToProps)(PatternRedirectPage);