---
title: Why we're moving to component theming
---

A week ago, we released [Mannequin](https://mannequin), which we're calling a "Component theming tool for the web."  I wanted to explain what we mean by "Component theming," and to explain why we're (belatedly) switching to this approach for our Drupal development.

## Our Story 

We used to be terrible at theming.  Five or six years ago, the sites we built were consistent in one way - they all had inconsistent page margins, big cross-browser issues, and enough CSS to theme a dozen sites.  As a developer, I used to hate jumping betweeen our projects, because each one had it's own rats-nest of CSS, and every time I made a change, it broke parts of the site that nobody even knew existed.

That changed when we discovered Foundation.  Foundation solved the vast majority of our consistency and cross-browser problems right away.  It was a night-and-day shift for us.  We'd just apply a few simple classes to our markup, and our theming was "roughed in".  Some custom styles would be written to cover the rest, but in general we were writing much less code, which made the bugs easier to find and fix.  There was still a pretty major problem though - small changes still had a tendency to break things in unexpected ways.

These days, we're starting a new chapter in our journey toward front-end excellence... the shift to Component theming.  Component theming is theming based on chunks of markup ("Components") rather than pages.  If you haven't read Brad Frost's excellent [Atomic Design](http://atomicdesign.bradfrost.com/), you should.  It's a great intro to the topic, although the terminology is a little different from what we'll use here... Atomic Design is as much a specification for design as it is for development, and what we're primarily interested in here is the development portion (theming).

## What we're changing

Long story short, for many of our newer projects, we've been shifting away from using globally available utility classes (such as Foundation's `.row` and `.column`), and toward theming specific templates.  To use a very simple example, let's consider how we might theme something like a horizontally laid-out card:

[Foundation Markup](https://codepen.io/rbayliss/pen/EweEqG):
https://codepen.io/rbayliss/pen/EweEqG
<p data-height="265" data-theme-id="0" data-slug-hash="EweEqG" data-default-tab="html,result" data-user="rbayliss" data-embed-version="2" data-pen-title="Foundation - Card" class="codepen">See the Pen <a href="https://codepen.io/rbayliss/pen/EweEqG/">Foundation - Card</a> by Rob Bayliss (<a href="https://codepen.io/rbayliss">@rbayliss</a>) on <a href="https://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

[Component Markup](https://codepen.io/rbayliss/pen/YrOLQq):
<p data-height="265" data-theme-id="0" data-slug-hash="YrOLQq" data-default-tab="html,result" data-user="rbayliss" data-embed-version="2" data-pen-title="Foundation - Card" class="codepen">See the Pen <a href="https://codepen.io/rbayliss/pen/YrOLQq/">Foundation - Card</a> by Rob Bayliss (<a href="https://codepen.io/rbayliss">@rbayliss</a>) on <a href="https://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

The thing that's immediately obvious is that we've gotten rid of the Foundation layout classes.  This forces us to handle the layout ourselves, and to do that, we're targeting the markup _we know this component uses_ directly.  What's more, all of our CSS we're using for this component is scoped to the `HorizontalCard` class, so there's no chance it will leak out into the global styling.  We might say that this component is very isolated from the rest of the system as compared to the Foundation version, which is going to depend on the unscoped `.row` and `.column` selectors.  As a result, when the client adds feedback at the end of the sprint that the `HorizontalCard` title is 1px too narrow, we can make that fix without touching anything but the `HorizontalCard` CSS.  That is to say - refactoring just got a whole lot easier.  Don't believe me?  Check out the next example, where we've refactored the component to use CSS grid:

[Component Markup with CSS Grid](https://codepen.io/rbayliss/pen/mBGLgB)
<p data-height="265" data-theme-id="0" data-slug-hash="mBGLgB" data-default-tab="css,result" data-user="rbayliss" data-embed-version="2" data-pen-title="Foundation - Card" class="codepen">See the Pen <a href="https://codepen.io/rbayliss/pen/mBGLgB/">Foundation - Card</a> by Rob Bayliss (<a href="https://codepen.io/rbayliss">@rbayliss</a>) on <a href="https://codepen.io">CodePen</a>.</p>
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>

### But what about shared styles?

There will always be shared styles. Even if we added no unscoped CSS or utility classes, browsers have their own stylesheet that would change the look of your components.  The key is to keep these as minimal as possible.  We've still be applying the following two Foundation mixins globally on the projects where we've been working with components:

```scss
# _global.scss
@include foundation-global-styles;
@include foundation-typography;
```

This gives us a good global baseline that gets shared by all components, but you need to be extremely judicious about what gets included in unscoped CSS, since changing it later on will affect every single component (exactly what we want to avoid).

## How we're changing

Last week, we released [Mannequin](https://mannequin.io), a tool for isolating your templates as components and rendering them with sample data.  Going forward, we plan to use Mannequin on all of our new projects from the get-go.  Rather than writing extremely Drupal-specific templates, our front end developers will be writing a much cleaner version in Mannequin, then we'll be wiring it into Drupal using what we're calling "glue templates."

```twig
DEMO
```
Mannequin does not dictate that we use a glue template here - we could be writing a single `node.html.twig` and use it with Mannequin just fine.  But glue templates give us two important benefits.  First, we're free to reuse the component template just by writing another glue template that includes it, keeping our project DRY while making things nice and discoverable by leaving the Drupal defined templates in place.  Second, writing our templates without considering Drupal's funky data structures means we can work with developers who don't know their nodes from their assets (non-Drupal developers). As much as I poke fun, we're excited to leaving a lot of the Drupalisms behind.


## That's all for now!

Next week looks like a a busy one!  If you're going to BADCamp and are interested in Component based theming, please find us to talk, or come to [our session on Mannequin](https://2017.badcamp.net/session/coding-development/intermediate/introducing-mannequin)!





Foundation SASS:
----------------
Pros:
* There's a standard way of doing things (just add classes)
* You can modify the core provided classes globally
* You can define additional "common" classes to use

Cons:
* Lots of selector reuse - Since things are provided in mix-n-match classes, the same classes end up getting used in many, many places.
* You can modify the core provided classes globally (globally modified classes ripple throughout the system)
* Lots of non-semantic markup.
* Refactoring is extremely hard.

OO/Component Theming
--------------------
Pros:
* Minimizes global styles - Only a few styles (normalize + typography) are applied globally, there's very little chance that global styles will change in a way that breaks your component.
* Encapsulation - each component is scoped entirely to a single selector using SASS.  No styles within that selector can leak out to another component unless that component is nested.
* Separation of concerns - Styling is really and truly separated from the markup.
* Refactorable - Since styles for each component do not overlap, drastic changes to a single component are virtually guaranteed not to break other components.  
* It's the "Right way" - Semantic markup is possible.

Cons:
* Difficulty - It's easier to use a utility class than to write the equivalent SASS for doing something simple like centering.
* Verbose - Generally requires more lines of SASS than the Foundation SASS approach, since you need to explictly `@include` mixins for each component.

