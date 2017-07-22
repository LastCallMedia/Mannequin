
import React from 'react';
import {Redirect, Switch, Route} from 'react-router-dom';
import {connect} from 'react-redux'
import {getPattern, getPatternUsedPatterns, getVariantFromPattern} from './selectors';
import {toggleInfo} from './actions';
import PatternInfo from './components/PatternInfo';
import RenderFrame from './components/RenderFrame';
import {VariantNotFound} from './components/NotFound';
import VariantSelector from './components/VariantSelector';
import PatternProblems from './components/PatternProblems';
import PatternTopBar from './components/PatternTopBar';
import {OpenButton, ViewInfoButton} from './components/Buttons';

const PatternOuterPage = ({pattern, showingInfo, match, toggleInfo}) => {
    const selector = pattern && pattern.variants.length > 1 ? <RoutedVariantSelector base={match.url} variants={pattern.variants} /> : null;
    const problems = pattern && pattern.problems.length ? <PatternProblems problems={pattern.problems} /> : null;
    const info = pattern ? <ConnectedVariantInfo base={match.url} pattern={pattern} /> : null;
    const actions = [
        <ViewInfoButton onClick={toggleInfo} />,
        <OpenVariantButton base={match.url} pattern={pattern} />,
    ];
    return (
        <main className="PatternOuterPage">
            <PatternTopBar actions={actions} selector={selector} title={pattern ? pattern.name : 'Loading...'} />
            {problems}
            <Switch>
                <Route path={`${match.path}`} exact render={({match}) => {
                    if(pattern && pattern.variants.length) {
                        return <Redirect to={`${match.url}/variant/${pattern.variants[0].id}`}/>
                    }
                    return <span>Loading...</span>
                }}/>
                {/* Then try to match on the exact variant. */}
                {pattern && pattern.variants.map(variant => {
                      return <Route key={variant.id} path={`${match.path}/variant/${variant.id}`} render={(p) => {
                          return <RenderFrame src={variant.rendered} />
                      }} />
                })}
                <Route component={VariantNotFound} />
            </Switch>
            <div className={`Info ${showingInfo ? 'showing' : 'hiding'}`}>
                <button className="close-button" onChange={toggleInfo}></button>
                {showingInfo && info}
            </div>

        </main>
    )
}

const RoutedVariantSelector = (props) => {
    const {base, ...rest} = props;
    return <Route path={`${base}/variant/:vid`} children={(routeProps) => {
        const onChange = (e) => {
            routeProps.history.push(`${base}/variant/${e.target.value}`)
        }
        const value = routeProps.match ? routeProps.match.params.vid : '';
        return <VariantSelector value={value} onChange={onChange} {...rest} />
    }}/>
};


const OpenVariantButton = connect()(({base, pattern}) => {
    return <Route path={`${base}/variant/:vid`} render={({match}) => {
        const variant = getVariantFromPattern(pattern, match.params.vid);
        return variant ? <OpenButton href={variant.source} /> : null;
    }} />
});


const iMapStateToProps = (state, ownProps) => {
    return {
        used: getPatternUsedPatterns(ownProps.pattern, state.patterns)
    }
}
const ConnectedVariantInfo = connect(iMapStateToProps)(({base, ...rest}) => {
    return <Route path={`${base}/variant/:vid`} children={({match}) => {
        const variant = match ? getVariantFromPattern(rest.pattern, match.params.vid) : undefined;
        return <PatternInfo variant={variant} {...rest} />
    }}/>
});



const mapStateToProps = (state, ownProps) => {
    return {
        pattern: getPattern(state, ownProps),
        showingInfo: state.info,
    }
}

const mapDispatchToProps = (dispatch, ownProps) => {
    return {
        toggleInfo: () => dispatch(toggleInfo())
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PatternOuterPage);