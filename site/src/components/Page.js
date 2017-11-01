import React from 'react'
import PropTypes from 'prop-types';
import './Page.scss';

export default function Page({ title, children, sidebar, editLink }) {
  return (
    <div className="Page">
      <h1 className="title">{title}</h1>
      <div className="content">{children}</div>
      {sidebar && <aside className="sidebar">{sidebar}</aside>}
      {editLink && <aside className="editlink">{editLink}</aside>}
    </div>
  )
}

Page.propTypes = {
  title: PropTypes.string.isRequired,
  children: PropTypes.node.isRequired,
  sidebar: PropTypes.node,
  editLink: PropTypes.node,
}
