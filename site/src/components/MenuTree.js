
import React from 'react';
import Link from 'gatsby-link';
import './MenuTree.scss';

export default function MenuTree({links}) {
    return (
        <ul className="PageMenu">
            {links.map(link => (
                <li key={link.to}>
                    <Link to={link.to}>{link.title}</Link>
                    {link.below.length > 0 && <MenuTree links={link.below} />}
                </li>
            ))}
        </ul>
    )
}