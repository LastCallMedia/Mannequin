import React from 'react'
import TopBar from './PageTopBar'
import Footer from './Footer'
import Helmet from 'react-helmet'
import PropTypes from 'prop-types';
import './PageWrapper.scss';

export default function PageWrapper({ title, description, children }) {
  return (
    <div className="PageWrapper">
      <Helmet>
        <title>{title} | Mannequin</title>
        <meta name="description" content={description} />
      </Helmet>
      <TopBar />
      {children}
      <Footer />
    </div>
  )
}

PageWrapper.propTypes = {
  title: PropTypes.string.isRequired,
  children: PropTypes.element.isRequired,
  description: PropTypes.string.isRequired,
}
