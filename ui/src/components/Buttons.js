
import React from 'react';

import {OpenNew} from '../Icon';

export const OpenButton = ({href}) => (
    <a className="OpenWindowButton" target="_blank" href={href}><OpenNew/></a>
)

export const ViewInfoButton = (props) => (
    <button className="PatternInfoButton" {...props}>View Pattern Info</button>
)