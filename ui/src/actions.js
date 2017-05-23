
export function fetchPatterns() {
  return (dispatch) => {
    dispatch({type: 'PATTERNS_FETCH_LOADING'});
    fetch('manifest.json')
      .then((res) => res.json())
      .then((res) => dispatch(updatePatterns(res)))
      .catch(err => dispatch(errorPatterns(err)));
  }
}

function updatePatterns(response) {
  return {
    type: 'PATTERNS_FETCH_SUCCESS',
    patterns: response.patterns,
    tags: response.tags
  }
}

function errorPatterns(err) {
  return {
    type: 'PATTERNS_FETCH_ERROR',
    error: err
  }
}

