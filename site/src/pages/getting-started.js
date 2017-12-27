import React from 'react'
import Link from 'gatsby-link'
import TopBar from '../components/HomeTopBar'
import Footer from '../components/Footer'
import Branding from '../components/Branding'
import Bubble, {
  BubbleLayer,
  BubbleCluster,
  BubbleLayerBoundary,
} from '../components/Bubble'
import ExtensionSelection from '../components/ExtensionSelection'
import Manny from '!svg-react-loader?name=Logo!../img/Logo.svg'

export default function IndexPage() {
  return (
    <div className="GettingStartedWrapper">
      <div className="GettingStarted">
        <a className="HomePageLink" href="/" title="Mannequin home">
          <Branding tiny />
        </a>
        <GettingStartedHero />
      </div>
      <Footer />
    </div>
  )
}

function GettingStartedPane() {
  return (
    <div className="GetStartedPane">
      <Manny className="FlexingManny" />
      <h2 id="GetStarted">Getting Started</h2>

      <div className="ChooseExtensionStep Step">
        <div className="intro">
          <h3>Choose a template</h3>
        </div>
        <ExtensionSelection />
      </div>
    </div>
  )
}

function GettingStartedHero() {
  return (
    <BubbleLayerBoundary className="GettingStartedHero">
      <div className="inner">
        <GettingStartedPane />
      </div>

      <BubbleLayer travel={5}>
        <Bubble
          size={130}
          thickness={20}
          duration={35}
          blur={2}
          top="67%"
          left="0"
          bottom="0"
          opacity={0.45}
        />
        <Bubble
          size={38}
          thickness={11}
          duration={9}
          blur={2}
          top="70%"
          right="6%"
          opacity={0.45}
        />
      </BubbleLayer>

      <BubbleLayer travel={15}>
        <Bubble
          size={85}
          thickness={13}
          duration={15}
          blur={6}
          top="60%"
          left="4%"
          opacity={0.25}
        />
        <Bubble
          size={75}
          thickness={13}
          duration={10}
          blur={6}
          top="30%"
          right="13%"
          opacity={0.25}
        />
      </BubbleLayer>

      <BubbleLayer travel={30}>
        <Bubble
          size={35}
          thickness={8}
          duration={9}
          blur={4}
          top="15%"
          left="15%"
          opacity={0.07}
        />
        <BubbleCluster duration={15} left="40%" top="72%">
          <Bubble size={30} thickness={10} blur={5} opacity={0.07} />
          <Bubble size={40} thickness={13} blur={5} opacity={0.07} />
        </BubbleCluster>
      </BubbleLayer>
    </BubbleLayerBoundary>
  )
}