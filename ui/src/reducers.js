import { combineReducers } from 'redux';

const components = (state = [], action) => {
  switch (action.type) {
    case 'COMPONENTS_FETCH_SUCCESS':
      return action.components;
    default:
      return state;
  }
};

const error = (state = false, action) => {
  switch (action.type) {
    case 'COMPONENTS_FETCH_SUCCESS':
      return false;
    case 'COMPONENTS_FETCH_ERROR':
      return action.error;
    default:
      return state;
  }
};

const loading = (state = 'complete', action) => {
  switch (action.type) {
    case 'COMPONENTS_FETCH_LOADING':
      return 'loading';
    case 'COMPONENTS_FETCH_SUCCESS':
      return 'complete';
    default:
      return state;
  }
};

const drawer = (state = false, action) => {
  switch (action.type) {
    case 'DRAWER_TOGGLE':
      return !state;
    default:
      return state;
  }
};

const quickLinks = (state = [], action) => {
  switch (action.type) {
    case 'COMPONENT_VIEW':
      const cid = action.component.id;
      const idx = state.indexOf(cid);
      let newState = state.slice(0);
      if (-1 !== idx) {
        // Pop the item out of the array.
        newState.splice(idx, 1);
      }
      newState.unshift(cid);
      return newState.slice(0, 5);
    default:
      return state;
  }
};

const info = (state = false, action) => {
  switch (action.type) {
    case 'INFO_TOGGLE':
      return !state;
    default:
      return state;
  }
};

export default combineReducers({
  components,
  loading,
  error,
  drawer,
  quickLinks,
  info
});
