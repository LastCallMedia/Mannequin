import React from 'react'
import Link from 'gatsby-link'
import TopBar from '../components/HomeTopBar'
import Footer from '../components/Footer'
import Branding from '../components/Branding'
import Bubble, {BubbleLayer, BubbleCluster, BubbleLayerBoundary} from '../components/Bubble';
import MouseIcon from '../components/MouseIcon';
import DrupalLogo from '../img/drupal.png';
import TwigLogo from '../img/twig.png';
import HtmlLogo from '../img/html.png';
import Manny from '!svg-react-loader?name=Logo!../img/Logo.svg'

export default function IndexPage() {
  return (
    <div className="HomeWrapper">
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
  )
}

function HomepageHero() {
  return (
    <BubbleLayerBoundary className="HomepageHero">
      <div className="inner">
        <Branding large slogan />
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
              className="button blue expanded"
            >
              <span className="text">View Demo</span>
            </a>
          </li>
        </ul>
      </div>
      <BubbleLayer travel={100}>
          <Bubble size={40} thickness={4} duration={5} blur={3} left='0' bottom="0" />
          <Bubble size={29} thickness={3} duration={4} blur={3} top="87px" right="25%" />
      </BubbleLayer>
      <BubbleLayer travel={50}>
        <BubbleCluster duration={8} left="180px" top="12px">
          <Bubble size={5} thickness={2} />
          <Bubble size={9} thickness={2} />
        </BubbleCluster>
        <Bubble size={18} thickness={2} duration={8} left="50%" top="54px" />
        <Bubble size={8} thickness={2} duration={3} right="20%" bottom="-73px" />
      </BubbleLayer>
    </BubbleLayerBoundary>
  )
}

function AboutProductPane() {
  return (
    <div className="AboutProductPane">
      <BubbleLayerBoundary className="inner">
        <div className="note">
          <h2>Introduction</h2>
          <p>Mannequin bridges the gap between design and development by rendering templates in an isolated environment.</p>
          <Link to="/about" className="button dashing-icon"><i className="icon icon-right"></i><span className="text">Explore</span></Link>
        </div>
        <div className="shot-casing">
        <div className="shot"></div>
        </div>
        <BubbleLayer>
          <Bubble size={30} thickness={3} left="52%" bottom="0" />
        </BubbleLayer>
      </BubbleLayerBoundary>
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
            <div className="img-container"><img src={DrupalLogo} alt="Drupal Logo" height="43" width="160" /></div>
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
          <h3><span>3.</span>Start a live development server</h3>
        </div>
        <div className="content">
            <code>vendor/bin/mannequin start</code>
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