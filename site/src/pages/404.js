import React from 'react'
import Page from '../components/Page'
import PageWrapper from '../components/PageWrapper'

export default function NotFoundPage() {
  const menu = [{ to: '/', title: 'Home', below: [] }]
  return (
    <Page title="Not Found" menu={menu}>
      <p>Oops! We couldn't find the page you were looking for!</p>
    </Page>
  )
}
