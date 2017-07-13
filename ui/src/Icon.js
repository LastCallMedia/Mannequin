import React from 'react';
import './Icon.css';

/**
 * Icons.  Embed the SVG directly here, stripping out all classes/IDs.
 */

export const CloseArrow = () => {
  return(
    <svg className="Icon CloseArrow" x="0px" y="0px" viewBox="0 0 16 16">
      <path d="M8,0L6.6,1.4L12.2,7H0v2h12.2l-5.6,5.6L8,16l8-8L8,0z"/>
    </svg>
  )
};

export const OpenNew = () => (
  <svg version="1.1" className="Icon OpenNew" x="0px" y="0px" viewBox="0 0 14 17.4">
      <path d="M2.5,4.5h6c1.1,0,2,0.9,2,2v8.4c0,1.1-0.9,2-2,2h-6c-1.1,0-2-0.9-2-2V6.5C0.5,5.4,1.4,4.5,2.5,4.5z"/>
      <path d="M5.5,0.5h6c1.1,0,2,0.9,2,2v8.4c0,1.1-0.9,2-2,2h-6c-1.1,0-2-0.9-2-2V2.5C3.5,1.4,4.4,0.5,5.5,0.5z"/>
  </svg>
)


export const Arrow = () => {
  return (
    <svg className="Icon Arrow" viewBox="0 0 6 9.3">
      <polygon points="6,8.2 2.3,4.7 6,1.1 4.9,0 0,4.7 4.9,9.3 "/>
    </svg>
  )
}

export const Search = () => (
  <svg className="Icon Search" x="0px" y="0px" viewBox="0 0 17.5 17.5">
    <path d="M12.5,11h-0.8l-0.3-0.3c1-1.1,1.6-2.6,1.6-4.2C13,2.9,10.1,0,6.5,0S0,2.9,0,6.5S2.9,13,6.5,13c1.6,0,3.1-0.6,4.2-1.6 l0.3,0.3v0.8l5,5l1.5-1.5L12.5,11z M6.5,11C4,11,2,9,2,6.5S4,2,6.5,2S11,4,11,6.5S9,11,6.5,11z"/>
  </svg>
)