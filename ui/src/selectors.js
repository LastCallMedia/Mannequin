import {createSelector} from 'reselect';

/**
 * These reselect selectors pull data out of redux state based on URL params.
 */
export const getPatternsFromState = state => state.patterns;
export const getSelectedPatternId = (state, ownProps) => ownProps.match.params.pattern;
export const getSelectedVariantId = (state, ownProps) => ownProps.match.params.variant;

export const getPattern = createSelector(
    [getPatternsFromState, getSelectedPatternId],
    (patterns, patternId) => {
        return patterns.filter(p => p.id === patternId).pop();
    }
)
export const getVariant = createSelector(
    [getPattern, getSelectedVariantId],
    (pattern, variantId) => {
        return pattern ? pattern.variants.filter(s => s.id === variantId).pop() : undefined;
    }
)
export const getUsed = createSelector(
    [getPatternsFromState, getPattern],
    (patterns, pattern) => {
        return pattern ? pattern.used.map(id => (
            patterns.filter(p => p.id === id).pop()
        )) : [];
    }
)