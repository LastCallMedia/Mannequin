import React from 'react'
import Branding from './Branding'
import MailchimpForm from 'react-mailchimp-subscribe';

import './Footer.scss';

export default function Footer() {
  return (
    <footer className="page-footer">
      <Branding />
      <a
        className="footer-link button dashing-icon"
        href="https://github.com/LastCallMedia/Mannequin/issues"
      >
        <i className="icon icon-github" />
        <span className="text">Get Support on Github</span>
      </a>

        <MailchimpForm
            className="Newsletter"
            action="https://lastcallmedia.us9.list-manage.com/subscribe/post?u=d60cafbefdea2f1ee497ac747&amp;id=3dcf6c9dcc"
            messages={{
                inputPlaceholder: 'Subscribe for updates',
                btnLabel: 'Subscribe',
                sending: 'Sending...',
                success: 'Subscribed! Please check your e-mail and follow the confirmation link.',
                error: 'Oops, we weren\'t able to subscribe you!',
            }}
        />
    </footer>
  )
}
