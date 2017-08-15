
import React from 'react';
import PropTypes from 'prop-types';
import './PatternTopBar.css';

const PatternTopBar = ({title, selector, actions}) => {
    const theseActions = actions ? React.cloneElement(actions, {
        className: 'actions'
    }) : null;
    return (
        <div className="PatternTopBar">
            <div className="inner">
                <h4 className="name">{title}</h4>
                <div className="variant">{selector}</div>
                {theseActions}
            </div>
        </div>
    )
}
PatternTopBar.propTypes = {
    title: PropTypes.string,
    selector: PropTypes.node,
    actions: PropTypes.element,
}
PatternTopBar.defaultProps = {
    title: '',
    selector: [],
    actions: null,
}

export default PatternTopBar;