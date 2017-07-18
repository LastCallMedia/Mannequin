
import React from 'react';
import {Link} from 'react-router-dom';

import './Card.css';

const Card = ({title, subtitle, to}) => (
    <article className="Card">
        <Link to={to}>
            <h6>{subtitle}</h6>
            <h5>{title}</h5>
        </Link>
    </article>
)

export default Card;