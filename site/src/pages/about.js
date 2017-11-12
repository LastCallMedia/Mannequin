
import React from 'react';
import Page from '../components/Page';
import Link from 'gatsby-link';


export default function AboutPage() {
    const tree = [
        {to: '/', title: 'Home', below: []},
        {to: '/about', title: 'About Mannequin', below: [], active: true},
    ]
    return (
        <Page title="About Mannequin" description="All about the Mannequin component theming tool" menu={tree}>
            {/* What it is */}
            <p>Mannequin is an open source tool that lets you work on your website's templates in an isolated way. In practice, that means that Mannequin provides a live development server to show you the output of one template at a time, and a way to pump data (variables) into that template so you can see how it will look in the "real world". Mannequin currently supports using <Link to="/extensions/html">HTML files</Link>, <Link to="/extensions/twig">Twig templates</Link>, and <Link to="/extensions/drupal">Drupal Twig templates</Link> as components, but we would love to extend support to other frameworks and content management systems in the future.</p>

            <h2>Where it came from</h2>
            <p>In 2016, we (<a href="https://lastcallmedia.com">Last Call Media</a>) started moving toward component theming as a way to improve the overall quality of the sites we build.

                You can <a href="https://lastcallmedia.com/blog/why-component-theming">read all about our journey here</a>, but the things we wanted to achieve were:</p>
            <ul>
                <li>Promote reuse and organization within our projects</li>
                <li>Separate the frontend work from the backend, so we could do both at once</li>
                <li>Simplify our workflow so we could codify and repeat it</li>
            </ul>
            <p>When we investigated the tools available for component theming in Drupal, a lot of them checked the first two boxes, but all of them fell a little short on the "simplicity" requirement.  So Mannequin was born as a way to bring simplicity to the component theming workflow.  You can read more about our reasoning in <a href="https://lastcallmedia.com/blog/introducing-mannequin">this blog post</a>.</p>

            <h2>Mannequin's Philosophy</h2>
            <p>Mannequin is not an opinionated tool.  It is intended to work with your templates wherever they are, in a way that more or less mirrors how your production application will use them.  It doesn't care what CSS methodology you use, where your templates live, or what terminology you use to group them.</p>
            <p>There is a downside to being unopinionated; it's harder to give people a prescriptive, step by step workflow to follow when they're getting started. We've tried hard to write documentation that makes sense and suggests a way to work, but if you run into any questions or trouble along the way, feel free to reach out <a href="https://github.com/LastCallMedia/Mannequin/issues">in the issue queue</a>.</p>
            <h2>Credits</h2>
            <p>We would be remiss if we didn't mention <a href="http://patternlab.io/">Pattern Lab</a> as our primary influence. Pattern Lab, along with Brad Frost's incredible <a href="http://atomicdesign.bradfrost.com/">Atomic Design</a> book, paved the way for tools like Mannequin, and helped form the practice of component theming in the industry.</p>
        </Page>
    )
}
