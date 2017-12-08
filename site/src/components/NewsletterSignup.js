import React from 'react'
import MailchimpForm from 'react-mailchimp-subscribe'
import './NewsletterSignup.scss'

export default function NewsletterSignup() {
  return (
    <div className="NewsletterSignup">
      <label htmlFor="EMAIL">Subscribe for updates</label>
      <MailchimpForm
        action='https://lastcallmedia.us9.list-manage.com/subscribe/post?u=d60cafbefdea2f1ee497ac747&amp;id=3dcf6c9dcc'
        messages={{
          inputPlaceholder: 'Email',
          btnLabel: '',
          sending: 'Sending...',
          success: 'Success!',
          error: 'Enter email before submitting',
        }}
      />
    </div>
  )
}
