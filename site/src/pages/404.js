import React from 'react';
import Page from '../components/Page';
import PageWrapper from '../components/PageWrapper';

export default function NotFoundPage() {
    return (
        <PageWrapper title="404">
            <Page title="Not Found">
                <p>Oops!  We couldn't find the page you were looking for!</p>
            </Page>
        </PageWrapper>
    )
}
