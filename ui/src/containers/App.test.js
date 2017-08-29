import React from 'react';
import ReactDOM from 'react-dom';
import { App } from './App';

// Use a dummy component for the homepage.
jest.mock('./HomePage', () => () => <span />);

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<App />, div);
});
