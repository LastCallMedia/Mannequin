
import React from 'react';
import {OpenNew} from '../../Icon';

export default ({href}) => (
    <a className="OpenWindowButton" target="_blank" href={href}><OpenNew/></a>
)