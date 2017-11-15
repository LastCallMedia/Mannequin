import React from 'react'
import MailchimpForm from 'react-mailchimp-subscribe'
import './NewsletterSignup.scss'

export default function NewsletterSignup() {
  return (
    <MailchimpForm
      className="NewsletterSignup"
      action="https://lastcallmedia.us9.list-manage.com/subscribe/post?u=d60cafbefdea2f1ee497ac747&amp;id=3dcf6c9dcc"
      messages={{
        inputPlaceholder: 'Subscribe for updates',
        btnLabel: 'Subscribe',
        sending: 'Sending...',
        success:
          'Subscribed! Please check your e-mail and follow the confirmation link.',
        error: "Oops, we weren't able to subscribe you!",
      }}
    />
  )
}
