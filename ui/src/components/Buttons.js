
import React from 'react';
import './Buttons.css';
import cx from 'classnames';

import {OpenNew} from '../Icon';

export const OpenButton = ({href}) => (
    <a className="OpenWindowButton" target="_blank" href={href}><OpenNew/></a>
)

export const ViewInfoButton = (props) => (
    <button className="PatternInfoButton" {...props}>View Pattern Info</button>
)

export const InfoCloseButton = ({className, ...rest}) => (
    <button className={cx('InfoCloseButton', className)} {...rest}><span aria-hidden="true">&times;</span></button>
)