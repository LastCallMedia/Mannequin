
import React from 'react';
import PropTypes from 'prop-types';
import './PatternTopBar.css';

const PatternTopBar = ({title, selector, actions}) => {
    return (
        <div className="PatternTopBar">
            <div className="inner">
                <h4 className="name">{title}</h4>
                <div className="variant">{selector}</div>
                <ul className="actions">
                    {React.Children.map(actions, (c,i) => (
                        <li key={i}>{c}</li>
                    ))}
                </ul>
            </div>
        </div>
    )
}
PatternTopBar.propTypes = {
    title: PropTypes.string,
    selector: PropTypes.node,
    actions: PropTypes.arrayOf(PropTypes.element),
}
PatternTopBar.defaultProps = {
    title: '',
    selector: [],
    actions: [],
}

export default PatternTopBar;