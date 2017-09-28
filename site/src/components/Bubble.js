
import React, {Component} from 'react';
import PropTypes from 'prop-types';
import Plx from 'react-plx';
import cx from 'classnames';
import "./Bubble.scss";

export default function Bubble({size = 30, thickness = 1, duration = 3, blur = false, className = ''}) {
    const bubbleClass = cx({
        'Bubble': true,
        [`${className}`]: true,
        'blurry': blur
    });
    const style = {
        width: `${size}px`,
        height: `${size}px`,
        borderWidth: thickness,
        animationDuration: `${duration}s`,
    }
    return <b className={bubbleClass} style={style} />
}

Bubble.propTypes = {
    size: PropTypes.number,
    thickness: PropTypes.number,
    blur: PropTypes.bool,
    className: PropTypes.string
}

export function BubbleCluster({children, duration = 3, className =''}) {
    const style = {
        animationDuration: `${duration}s`
    }
    return (
        <b className={`BubbleCluster ${className}`} style={style}>
            {children}
        </b>
    );
}

BubbleCluster.propTypes = {
    className: PropTypes.string,
    duration: PropTypes.number,
    children: PropTypes.element.isRequired,
}

export function BubbleLayer({children}) {
    return (
        <Plx className="BubbleLayer" parallaxData={[{
            start: 'top',
            duration: 'height',
            properties: [
                {
                    startValue: 0,
                    endValue: 130,
                    unit: 'px',
                    property: 'translateY'
                }
            ]
        }]}>
            {children}
        </Plx>
    )
}

BubbleLayer.propTypes = {
    children: PropTypes.element.isRequired
}