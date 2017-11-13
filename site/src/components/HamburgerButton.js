import React from 'react'
import './HamburgerButton.scss'

export default function HamburgerButton({ onClick }) {
  return (
    <button className="HamburgerButton" onClick={onClick}>
      <i className="HamburgerIcon" />
    </button>
  )
}
