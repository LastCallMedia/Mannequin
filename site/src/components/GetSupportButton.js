import React from 'react'
import GithubIcon from 'react-icons/lib/go/mark-github'
import './GetSupportButton.scss'

export default function GetSupportButton() {
  return (
    <a
      className="GetSupportButton"
      href="https://github.com/LastCallMedia/Mannequin/issues"
    >
      <GithubIcon className="icon" />
      <span className="text">Get Support on Github</span>
    </a>
  )
}
