import React from 'react';
import { OpenNew } from '../Icons';
import './OpenWindowButton.css';

export default ({ href }) => (
  <a className="OpenWindowButton" target="_blank" href={href}>
    <OpenNew />
  </a>
);
