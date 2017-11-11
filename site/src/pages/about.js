
import React from 'react';
import Page from '../components/Page';


export default function AboutPage() {

    return (
        <Page title="About Mannequin" description="All about the Mannequin component theming tool">
            {/* What it is */}
            <p>Mannequin is one thing: a tool that lets you work on your website's templates in an isolated way. In practice, that means that Mannequin provides a live development server to show you the output of one template at a time, and a way to pump data (variables) into that template so you can do meaningful work.</p>

            <h2>Where it came from</h2>
            <p>In 2016, we (<a href="https://lastcallmedia.com">Last Call Media</a>) started moving toward component theming. You can <a href="https://lastcallmedia.com/blog/why-component-theming">read all about our journey here</a>, but to sum up what we've learned along the way:</p>
            <ul>
                <li>Promote reuse and organization within our projects</li>
                <li>Separate the frontend work from the backend, and have them run simultaneously</li>
                <li>Use the simplest possible workflow</li>
            </ul>
            <p>When we looked around at what other tools were out there, we found <a>Pattern Lab.</a>  Pattern Lab checks 2/3 boxes, but we found the workflow and integration to be overly complicated.  So we set to work creating Mannequin.  You can read more about our reasoning in <a href="https://lastcallmedia.com/blog/introducing-mannequin">this blog post</a>.</p>

            <h2>Mannequin's Philosophy</h2>
            <p>Mannequin is not an opinionated tool.  It is intended to work with your templates wherever they are, in a way that more or less mirrors how your production application will use them.  We don't care about the terminology you use, where you keep your templates, or what you had for breakfast.</p>
            <p>There is a downside to being unopinionated, which is that there is not prescriptive workflow we can give you to help you learn how to use it. We've tried hard to write documentation that makes sense and suggests a way to work, but if you run into any questions or trouble along the way, feel free to reach out <a href="https://github.com/LastCallMedia/Mannequin/issues">in the issue queue</a>.</p>
        </Page>
    )

}

/**
 Points:

 * Theming shouldn't require data structures, or even a database.
 * We should promote reuse of styled elements.

 */