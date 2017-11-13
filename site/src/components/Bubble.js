import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Plx from 'react-plx'
import cx from 'classnames'
import './Bubble.scss'

export default function Bubble(props) {
  const { size = 30, thickness = 1, duration = 3, blur = 0 } = props
  const { left, right, top, bottom, opacity } = props
  const { className = '' } = props
  const bubbleClass = cx({
    Bubble: true,
    [`${className}`]: true,
  })
  const style = {
    width: `${size}px`,
    height: `${size}px`,
    borderWidth: thickness,
    animationDuration: `${duration}s`,
    WebkitAnimationDuration: `${duration}s`,
    opacity,
    left,
    right,
    top,
    bottom,
  }
  if (blur > 0) {
    style.filter = `blur(${blur}px)`
  }

  return <b className={bubbleClass} style={style} />
}
Bubble.propTypes = {
  size: PropTypes.number,
  thickness: PropTypes.number,
  blur: PropTypes.number,
  className: PropTypes.string,
  left: PropTypes.string,
  right: PropTypes.string,
  top: PropTypes.string,
  bottom: PropTypes.string,
  opacity: PropTypes.number,
}

export function BubbleCluster(props) {
  const { children, duration = 3, className = '' } = props
  const { left, right, top, bottom } = props
  const style = {
    animationDuration: `${duration}s`,
    left,
    right,
    top,
    bottom,
  }
  return (
    <b className={`BubbleCluster ${className}`} style={style}>
      {children}
    </b>
  )
}
BubbleCluster.propTypes = {
  className: PropTypes.string,
  duration: PropTypes.number,
  children: PropTypes.arrayOf(PropTypes.element),
}

export function BubbleLayer({ children, travel = 100 }) {
  return (
    <Plx
      className="BubbleLayer"
      parallaxData={[
        {
          start: 'top',
          duration: 'height',
          properties: [
            {
              startValue: 0,
              endValue: travel,
              unit: 'px',
              property: 'translateY',
            },
          ],
        },
      ]}
    >
      {children}
    </Plx>
  )
}
BubbleLayer.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.node,
    PropTypes.arrayOf(PropTypes.node),
  ]),
  travel: PropTypes.number,
}

export function BubbleLayerBoundary({ children, className }) {
  return <div className={cx('BubbleLayerBoundary', className)}>{children}</div>
}
BubbleLayerBoundary.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.node,
    PropTypes.arrayOf(PropTypes.node),
  ]),
  className: PropTypes.string,
}
