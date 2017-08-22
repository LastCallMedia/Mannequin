import React from 'react';
import CssTransition from 'react-transition-group/CSSTransition';

import './SlideInFromBottom.css';

export default ({ children, ...rest }) =>
  <CssTransition {...rest} timeout={500} classNames="SlideInFromBottom">
    {children}
  </CssTransition>;
