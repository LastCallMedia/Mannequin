import React, { Component } from 'react'
import NewsletterSignup from './NewsletterSignup'
import GetSupportButton from './GetSupportButton'
import MenuTree from './MenuTree'
import Link from 'gatsby-link'
import './PageNav.scss'

export default function PageNav({ menu, section, className }) {
  return (
    <nav className={`${className} PageNav`}>
      {section && <SectionHeader section={section} />}
      <MenuTree links={menu} />
      <div className="buttons">
        <NewsletterSignup />
        <GetSupportButton />
      </div>
    </nav>
  )
}

function SectionHeader({ section }) {
  return (
    <Link to="/#GetStarted" className="SectionHeader">
      <h3>{section}</h3>
      <h5>Change Extension</h5>
    </Link>
  )
}
