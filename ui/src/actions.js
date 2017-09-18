export function fetchComponents() {
  return dispatch => {
    dispatch({ type: 'COMPONENTS_FETCH_LOADING' });
    fetch('manifest.json', {credentials: 'same-origin'})
      .then(res => res.json())
      .then(res => dispatch(updateComponents(res)))
      .catch(err => dispatch(errorComponents(err)));
  };
}

function updateComponents(response) {
  return {
    type: 'COMPONENTS_FETCH_SUCCESS',
    components: response.components
  };
}

function errorComponents(err) {
  return {
    type: 'COMPONENTS_FETCH_ERROR',
    error: err
  };
}

export function toggleDrawer() {
  return {
    type: 'DRAWER_TOGGLE'
  };
}

export function componentView(component) {
  return {
    type: 'COMPONENT_VIEW',
    component
  };
}

export function toggleInfo() {
  return {
    type: 'INFO_TOGGLE'
  };
}
