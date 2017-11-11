import React from 'react'
import PropTypes from 'prop-types';
import Branding from './Branding';
import Helmet from 'react-helmet'
import './Page.scss';

export default function Page({ title, description, children, sidebar }) {
  return (
    <div className="Page">
        <Helmet>
            <title>{title} | Mannequin</title>
            <meta name="description" content={description} />
        </Helmet>
      <div className="branding-wrap">
        <Branding tiny to={'/'} />
      </div>
      <main>
          <h1 className="title">{title}</h1>
          <div className="content">{children}</div>
      </main>
      {sidebar && <aside className="sidebar">{sidebar}</aside>}
    </div>
  )
}

Page.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.string,
  children: PropTypes.node.isRequired,
  sidebar: PropTypes.node,
}
