
import React from 'react';
import {Link} from 'react-router-dom';
import PropTypes from 'prop-types';

import './Card.css';

const Card = ({title, subtitle, to}) => (
    <article className="Card">
        <Link to={to}>
            <h6>{subtitle}</h6>
            <h5>{title}</h5>
        </Link>
    </article>
)
Card.propTypes = {
    /** Shows the primary name */
    title: PropTypes.string.isRequired,
    /** Shows categorization */
    subtitle: PropTypes.string,
    /** Router link path */
    to: PropTypes.string
};

export default Card;