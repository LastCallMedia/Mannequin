
import {combineReducers} from 'redux';

const patterns = (state = [], action) => {
  switch(action.type) {
    case 'PATTERNS_FETCH_SUCCESS':
      return action.patterns;
    default:
      return state;
  }
}

const error = (state = false, action) => {
  switch(action.type) {
    case 'PATTERNS_FETCH_SUCCESS':
      return false;
    case 'PATTERNS_FETCH_ERROR':
      return action.error;
    default:
      return state;
  }
}

const loading = (state = 'complete', action) => {
  switch(action.type) {
    case 'PATTERNS_FETCH_LOADING':
      return 'loading';
    case 'PATTERNS_FETCH_SUCCESS':
      return 'complete';
    default:
      return state;
  }
}

export default combineReducers({
  patterns,
  loading,
  error
});