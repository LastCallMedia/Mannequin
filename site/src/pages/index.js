import React from 'react'
import Link from 'gatsby-link'
import TopBar from '../components/HomeTopBar'
import Footer from '../components/Footer'
import Branding from '../components/Branding'
import Bubble, {BubbleLayer, BubbleCluster} from '../components/Bubble';
import MouseIcon from '../components/MouseIcon';
import DrupalLogo from '../img/drupal.png';
import TwigLogo from '../img/twig.png';
import HtmlLogo from '../img/html.png';
import Manny from '!svg-react-loader?name=Logo!../img/Logo.svg'

export default function IndexPage() {
  return (
      <div className="HomeWrapper">
          {/* @todo: Remove this grid-container*/}
        <div className="grid-container">
          <TopBar />
          <div className="Homepage">
            <HomepageHero />
            <MouseIcon />
            <AboutProductPane />
            <GettingStartedPane />
            <FurtherReadingPane />
          </div>
          <Footer />
        </div>
      </div>
  )
}

function HomepageHero() {
    return (
        <div className="HomepageHero">
          <Branding large slogan />
            {/* @todo: Bring in bubbles */}
          <ul className="links">
            <li>
              <a
                  href="https://github.com/LastCallMedia/Mannequin"
                  className="button dashing-icon expanded"
              >
                <i className="icon icon-github" />
                <span className="text">Get it on github</span>
              </a>
            </li>
            <li>
              <Link to="docs" className="button dashing-icon expanded">
                <i className="icon icon-document" />
                <span className="text">Documentation</span>
              </Link>
            </li>
            <li>
              <a
                  href="https://demo.mannequin.io"
                  className="button dashing-icon expanded"
              >
                <span className="text">View Demo</span>
              </a>
            </li>
          </ul>
            <BubbleLayer>
                <Bubble size={40} thickness={4} blur duration={5} className="bubble1" />
                <BubbleCluster className="cluster1" duration={8}>
                    <Bubble size={5} thickness={2} />
                    <Bubble size={9} thickness={2} />
                </BubbleCluster>
                <Bubble size={18} thickness={2} className="bubble2" />
                <Bubble size={29} thickness={3} duration={4} blur className="bubble3" />
                <Bubble size={8} thickness={2} className="bubble4" />
            </BubbleLayer>
        </div>
    )
}

function AboutProductPane() {
  return (
      <div className="AboutProductPane">
        <div className="inner">
          <div className="note">
            <p>Mannequin bridges the gap between design and development by previewing and rendering templates without a full development environment.</p>
            <a href="about.md" className="button dashing-icon"><i className="icon icon-right"></i><span className="text">Explore</span></a>
          </div>
          <div className="shot"></div>
            <BubbleLayer>
                <Bubble size={30} thickness={3} className="bubble1"/>
            </BubbleLayer>
        </div>
      </div>
  )
}

function GettingStartedPane() {
  return (
      <div className="GetStartedPane">
        <Manny className="logo" />
        <h2>Get Started</h2>

        <div className="ChooseExtensionStep Step">
          <div className="intro">
            <h3><span>1. </span>Choose the extension you would like to use and install it.</h3>
            <p>While we would eventually like to provide support for all major frameworks and CMS systems,
              these are the extensions that are currently available.</p>
          </div>
          <ul className="content">
            <li><Link to="/extensions/html">
              <div className="img-container"><img src={HtmlLogo} alt="HTML 5 Logo" height="76" width="54" /></div>
              <h4>HTML Extension</h4>
              <p>Display Static HTML files as Mannequin Components.</p>
            </Link></li>
            <li><Link to="/extensions/twig">
              <div className="img-container"><img src={TwigLogo} alt="Twig Logo" height="85" width="60" /></div>
              <h4>Twig Extension</h4>
              <p>Display Twig Templates as Mannequin Components.</p>
            </Link></li>
            <li><Link to="/extensions/drupal">
              <div className="img-container"><img src={DrupalLogo} align="Drupal Logo" height="43" width="160" /></div>
              <h4>Drupal Extension</h4>
              <p>Display Drupal 8 Twig Templates as Mannequin Components.</p>
            </Link></li>
          </ul>

        </div>
        <div className="ConfigureStep Step">
          <div className="intro">
            <h3><span>2. </span>Configure</h3>
          </div>
          <div className="content"><Link to="/docs/configuration" className="button dashing-icon"><i className="icon icon-right"></i><span className="text">Create a .mannequin.php file</span></Link></div>
        </div>
        <div className="RunStep Step">
          <div className="intro">
            <h3><span>3.</span>Start a live development server using vendor/bin/mannequin server.</h3>
          </div>
        </div>
      </div>
  )
}

function FurtherReadingPane() {
    return (
        <div className="FurtherReadingPane">
            <h2>Further Reading</h2>
            <ul className="links">
                <li><Link to="/docs/configuration">Configuration</Link></li>
                <li><Link to="/docs/components">Components</Link></li>
                <li><Link to="/extensions">Extensions</Link></li>
                <li><Link to="/docs/troubleshooting">Troubleshooting</Link></li>
            </ul>
        </div>
    )
}