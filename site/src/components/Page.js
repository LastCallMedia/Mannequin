import React from 'react'
import PropTypes from 'prop-types'

export default function Page({ title, children, sidebar }) {
  return (
    <div className="Page">
      <h1 className="title">{title}</h1>
      <div className="content">{children}</div>
      {sidebar && <aside className="sidebar">{sidebar}</aside>}
    </div>
  )
}

Page.propTypes = {
  title: PropTypes.string.isRequired,
  children: PropTypes.node.isRequired,
  sidebar: PropTypes.node,
}
