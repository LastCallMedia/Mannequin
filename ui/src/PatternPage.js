
import React from 'react';
import {Redirect, Switch, Route} from 'react-router-dom';
import {connect} from 'react-redux'
import {getPattern, getPatternUsedPatterns, getVariantFromPattern, getUsed} from './selectors';
import {toggleInfo} from './actions';
import PatternInfo from './components/PatternInfo';
import RenderFrame from './components/RenderFrame';
import {VariantNotFound} from './components/NotFound';
import VariantSelector from './components/VariantSelector';
import PatternProblems from './components/PatternProblems';
import PatternTopBar from './components/PatternTopBar';
import {OpenButton, ViewInfoButton, InfoCloseButton} from './components/Buttons';

import './PatternPage.css';

const PatternOuterPage = (props) => {
    const {pattern, match} = props;

    return (
        <Route path={`${match.url}/variant/:vid`} children={({match: variantMatch}) => {
            const variant = variantMatch ? getVariantFromPattern(pattern, variantMatch.params.vid) : null;
            return <PatternInnerPage base={match.url} variant={variant} {...props} />
        }}/>
    )
}

const PatternInnerPage = ({pattern, variant, showingInfo, match, toggleInfo, base, history}) => {
    const changeVariant = (e) => history.push(`${base}/variant/${e.target.value}`);
    const variants = pattern ? pattern.variants : [];
    const selector = variants.length ? <VariantSelector onChange={changeVariant} value={variant ? variant.id : ''} variants={variants} /> : null;
    const problems = pattern && pattern.problems.length ? <PatternProblems className="Content" problems={pattern.problems} /> : null;
    const actions = (
        <ul>
            {variant && <li><OpenButton href={variant.source} /></li>}
            <li><ViewInfoButton onClick={toggleInfo} /></li>
        </ul>
    );
    var info;
    if(pattern) {
        info = <ConnectedPatternInfo className={showingInfo ? 'showing' : 'hiding'} pattern={pattern} variant={variant} controls={<InfoCloseButton onClick={toggleInfo} />} />
    }
    else {
        info = null;
    }
    return (
        <main id="PatternInnerPage" className="PatternInnerPage no-scroll">
            <PatternTopBar actions={actions} selector={selector} title={pattern ? pattern.name : 'Loading...'} />
            {problems}
            <Switch>
                <Route path={`${match.path}`} exact render={({match}) => {
                    if(pattern && pattern.variants.length) {
                        return <Redirect to={`${match.url}/variant/${pattern.variants[0].id}`}/>
                    }
                    return <VariantNotFound text={'There are no variants for this pattern.'} />
                }}/>
                {/* Then try to match on the exact variant. */}
                {pattern && pattern.variants.map(variant => {
                    return <Route key={variant.id} path={`${match.path}/variant/${variant.id}`} render={(p) => {
                        return <RenderFrame src={variant.rendered} />
                    }} />
                })}
                <Route component={VariantNotFound} />
            </Switch>
            {info}
        </main>
    )
}


const iMapStateToProps = (state, ownProps) => {
    return {
        used: getPatternUsedPatterns(ownProps.pattern, state.patterns)
    }
}
const ConnectedPatternInfo = connect(iMapStateToProps)(PatternInfo);


const mapStateToProps = (state, ownProps) => {
    return {
        pattern: getPattern(state, ownProps),
        showingInfo: state.info,
        used: getUsed(state, ownProps)
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        toggleInfo: () => dispatch(toggleInfo()),
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PatternOuterPage);