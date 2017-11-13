import React from 'react'
import Link from 'gatsby-link'
import cx from 'classnames'
import PropTypes from 'prop-types'
import './MenuTree.scss'

export default function MenuTree({ links }) {
  return (
    <ul className="PageMenu">
      {links.map(link => (
        <li key={link.to} className={cx({ active: link.active })}>
          {link.to[0] === '#' ? (
            <a className="anchor-link" href={link.to}>
              {link.title}
            </a>
          ) : (
            <Link to={link.to}>{link.title}</Link>
          )}
          {link.below.length > 0 && <MenuTree links={link.below} />}
        </li>
      ))}
    </ul>
  )
}

export const shape = PropTypes.shape({
  to: PropTypes.string.isRequired,
  title: PropTypes.node.isRequired,
  active: PropTypes.bool.isRequired,
  below: PropTypes.array.isRequired,
})
