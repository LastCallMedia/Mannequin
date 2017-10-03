import React from 'react';
import ReactDOM from 'react-dom';

import { createStore, applyMiddleware, compose } from 'redux';
import { Provider } from 'react-redux';
import { loadState, saveState, observeStore } from './storage';
import { getStoredState } from './selectors';

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
 *
 * Specifically, use a reselect selector (getStoredState) to narrow state down
 * to the section we need (components and quicklinks), and avoid unnecessary
 * save calls.
 *
 * @see https://github.com/reactjs/redux/issues/303#issuecomment-125184409
 */
observeStore(store, getStoredState, saveState);

ReactDOM.render(
  <Provider store={store}>
    <App />
  </Provider>,
  document.getElementById('root')
);
