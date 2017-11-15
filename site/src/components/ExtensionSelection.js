import React from 'react'
import Link from 'gatsby-link'
import DrupalLogo from '../img/drupal.png'
import TwigLogo from '../img/twig.png'
import HtmlLogo from '../img/html.png'
import './ExtensionSelection.scss'

export default function ExtensionSelection({ className }) {
  return (
    <ul className={`ExtensionSelection ${className}`}>
      <li>
        <Link to="/extensions/html" className="Extension">
          <div className="img-container">
            <img src={HtmlLogo} alt="HTML 5 Logo" height="76" width="54" />
          </div>
          <h4>HTML</h4>
          <p>Display Static HTML files as Mannequin Components.</p>
        </Link>
      </li>
      <li>
        <Link to="/extensions/twig" className="Extension">
          <div className="img-container">
            <img src={TwigLogo} alt="Twig Logo" height="85" width="60" />
          </div>
          <h4>Twig</h4>
          <p>Display Twig Templates as Mannequin Components.</p>
        </Link>
      </li>
      <li>
        <Link to="/extensions/drupal" className="Extension">
          <div className="img-container">
            <img src={DrupalLogo} alt="Drupal Logo" height="43" width="160" />
          </div>
          <h4>Drupal</h4>
          <p>Display Drupal 8 Twig Templates as Mannequin Components.</p>
        </Link>
      </li>
    </ul>
  )
}
