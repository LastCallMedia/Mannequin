
import React, {Children} from 'react';
import PropTypes from 'prop-types';
import './PatternTopBar.css';

const PatternTopBar = ({title, selector, actions}) => {
    return (
        <div className="PatternTopBar">
            <div className="inner">
                <h4 className="name">{title}</h4>
                <div className="variant">{selector}</div>
                <div className="actions">
                    {actions}
                </div>
            </div>
        </div>
    )
}
PatternTopBar.propTypes = {
    title: PropTypes.string,
    selector: PropTypes.element,
    actions: PropTypes.node,

    // actions: PropTypes.arrayOf(PropTypes.element).isRequired
}
PatternTopBar.defaultProps = {
    actions: []
}

//
// const PatternTopBar = ({pattern, variant, openWindow, toggleInfo, changeVariant}) => {
//     return (
//         <div className="PatternTopBar">
//             <div className="inner">
//                 <h4 className="name">{pattern.name}</h4>
//                 <div className="variant"><VariantSelector variants={pattern.variants} selected={variant.id} changeVariant={changeVariant} /></div>
//                 <ul className="actions">
//                     <li><button onClick={toggleInfo} className="PatternInfoButton">View Pattern Info</button></li>
//                     <li><a onClick={openWindow} target="_blank" className="OpenWindowButton" href={variant.rendered}><OpenNew /></a></li>
//                 </ul>
//             </div>
//         </div>
//     )
// }
// PatternTopBar.propTypes = {
//     pattern: PropTypes.shape(PatternShape),
//     variant: PropTypes.shape(VariantShape),
//     openWindow: PropTypes.func.isRequired,
//     toggleInfo: PropTypes.func.isRequired,
//     changeVariant: PropTypes.func.isRequired,
// }

export default PatternTopBar;