export function fetchPatterns() {
  return dispatch => {
    dispatch({ type: 'PATTERNS_FETCH_LOADING' });
    fetch('manifest.json')
      .then(res => res.json())
      .then(res => dispatch(updatePatterns(res)))
      .catch(err => dispatch(errorPatterns(err)));
  };
}

function updatePatterns(response) {
  return {
    type: 'PATTERNS_FETCH_SUCCESS',
    patterns: response.patterns
  };
}

function errorPatterns(err) {
  return {
    type: 'PATTERNS_FETCH_ERROR',
    error: err
  };
}

export function toggleDrawer() {
  return {
    type: 'DRAWER_TOGGLE'
  };
}

export function patternView(pattern) {
  return {
    type: 'PATTERN_VIEW',
    pattern: pattern
  };
}

export function toggleInfo() {
  return {
    type: 'INFO_TOGGLE'
  };
}
