import React from 'react';
import { OpenNew } from '../Icons';

export default ({ href }) => (
  <a className="OpenWindowButton" target="_blank" href={href}>
    <OpenNew />
  </a>
);
