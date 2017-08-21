import {createSelector} from 'reselect';

/**
 * These reselect selectors pull data out of redux state based on URL params.
 */

// Simple selectors - only pull data out of state.
const getPatternsFromState = state => state.patterns;
const getQuickLinksFromState = state => state.quickLinks;
const getSelectedPatternId = (state, ownProps) => ownProps.match.params.pattern;
const getSelectedVariantId = (state, ownProps) => ownProps.match.params.vid;
export const getVariantFromPattern = (pattern, variantId) => (
    pattern ? pattern.variants.filter(s => s.id === variantId).pop() : undefined
);

// More complex selectors that do manipulation or filtering of data.
export const getPattern = createSelector(
    [getPatternsFromState, getSelectedPatternId],
    (patterns, patternId) => {
        return patterns.filter(p => p.id === patternId).pop();
    }
)
export const getVariant = createSelector(
    [getPattern, getSelectedVariantId],
    getVariantFromPattern
)
export const getUsed = createSelector(
    [getPatternsFromState, getPattern],
    (patterns, pattern) => {
        return pattern ? pattern.used.map(id => (
            patterns.filter(p => p.id === id).pop()
        )) : [];
    }
)

export const getQuicklinks = createSelector(
    [getPatternsFromState, getQuickLinksFromState],
    (patterns, ids) => {
        var quickLinks = patterns
            .filter(pattern => -1 !== ids.indexOf(pattern.id))
            .sort((p1, p2) => ids.indexOf(p1.id) - ids.indexOf(p2.id));

        return quickLinks;
    }
)