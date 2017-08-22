import React from 'react';
import ReactDOM from 'react-dom';

import { createStore, applyMiddleware, compose } from 'redux';
import { Provider } from 'react-redux';
import { loadState, saveState } from './storage';

import thunk from 'redux-thunk';
import App from './containers/App';
import reducers from './reducers';

const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

const initialState = loadState();

let store = createStore(
  reducers,
  initialState,
  composeEnhancers(applyMiddleware(thunk))
);

/**
 * Persist parts of the state to localstorage.
 */
store.subscribe(() => {
  saveState({
    quickLinks: store.getState().quickLinks
  });
});

ReactDOM.render(
  <Provider store={store}>
    <App />
  </Provider>,
  document.getElementById('root')
);
