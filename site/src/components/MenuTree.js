
import React from 'react';
import Link from 'gatsby-link';
import cx from 'classnames'
import './MenuTree.scss';

export default function MenuTree({links}) {
    return (
        <ul className="PageMenu">
            {links.map(link => (
                <li key={link.to} className={cx({active: link.active})}>
                    {link.to[0] === '#'
                        ? <a href={link.to}>{link.title}</a>
                        : <Link to={link.to}>{link.title}</Link>
                    }
                    {link.below.length > 0 && <MenuTree links={link.below} />}
                </li>
            ))}
        </ul>
    )
}