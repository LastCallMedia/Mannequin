import React from 'react'
import Branding from './Branding'
import NewsletterSignup from './NewsletterSignup'
import GetSupportButton from './GetSupportButton'

import './Footer.scss'

const today = new Date()

export default function Footer() {
  return (
    <footer className="page-footer">

      <div className="shift-up-by-half">
        <GetSupportButton />
      </div>

      <div className="inner">
        <Branding />

        <small className="copyright">Mannequin &copy; {today.getFullYear()}</small>

        <div className="learn-more">
          <h2>Learn More</h2>
          <a href="/about" className="button dashing expanded">
            <span className="text">About</span>
          </a>
          <a href="https://lastcallmedia.com/blog" className="button dashing expanded">
            <span className="text">Blog</span>
          </a>
        </div>

        <NewsletterSignup />
      </div>
    </footer>
  )
}
