import React from 'react'
import Branding from './Branding'
import NewsletterSignup from './NewsletterSignup'
import GetSupportButton from './GetSupportButton'

import './Footer.scss'

export default function Footer() {
  return (
    <footer className="page-footer">
      <Branding />
      <GetSupportButton />
      <NewsletterSignup />
    </footer>
  )
}
