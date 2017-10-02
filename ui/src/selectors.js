import { createSelector } from 'reselect';

/**
 * These reselect selectors pull data out of redux state based on URL params.
 */

// Simple selectors - only pull data out of state.
const getComponentsFromState = state => state.components;
const getQuickLinksFromState = state => state.quickLinks;
const getSelectedComponentId = (state, ownProps) =>
  ownProps.match.params.component;
const getSelectedSampleId = (state, ownProps) => ownProps.match.params.sid;
export const getSampleFromComponent = (component, sampleId) => {
  return component
    ? component.samples.filter(s => s.id === sampleId).pop()
    : undefined;
};

// More complex selectors that do manipulation or filtering of data.
export const getComponent = createSelector(
  [getComponentsFromState, getSelectedComponentId],
  (components, componentId) => {
    return components.filter(p => p.id === componentId).pop();
  }
);
export const getSample = createSelector(
  [getComponent, getSelectedSampleId],
  getSampleFromComponent
);
export const getUsed = createSelector(
  [getComponentsFromState, getComponent],
  (components, component) => {
    return component
      ? component.used.map(id => components.filter(p => p.id === id).pop())
      : [];
  }
);

export const getQuicklinks = createSelector(
  [getComponentsFromState, getQuickLinksFromState],
  (components, ids) => {
    var quickLinks = components
      .filter(component => -1 !== ids.indexOf(component.id))
      .sort((p1, p2) => ids.indexOf(p1.id) - ids.indexOf(p2.id));

    return quickLinks;
  }
);
