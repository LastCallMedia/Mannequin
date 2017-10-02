---
title: Components
description: All about components.
---
Templates are files that are used to generate HTML markup.  From Mannequin's perspective, a template becomes a component when:
1.  You tell a Mannequin [extension](../extensions.md) where to find it.
2.  It is enriched with YAML metadata describing the component.

Component metadata is written in YAML format, and describes the display properties of the component (name and description), as well as how to render it in one or more scenarios (called Samples).  Metadata can be specified in a .yml file living alongside the template file (supported for all extensions), or inside of the template file (supported by the Twig and Drupal extensions).

Here is an example metadata block that describes a HTML component called "button.twig".

```yaml
# button.yml
name: Button # what the component is called in the Mannequin UI.
description: An HTML Button # A long text description of this component.
```

## Group
Components can be grouped for display in the Mannequin UI using the `group` property.  Here is an example:
```yaml
# button.yml
...
group: Controls # Where to group it in the Mannequin UI.
```
When grouping, you can specify nested groups using the `>` character.  For example, you might use `Containers>Hero` if you have several Hero unit components that are similar.  In the UI, this would display a "Containers" menu item, with a child of "Hero" that has your templates below it. 

## Samples

Samples are sets of variables that are passed to the rendering engine when the component is rendered.  For example, imagine our `button.twig` template, which looks like this:

```twig
# button.twig
<button type="{{ type |default('button') }}" class="{{ clear ? 'clear ' : '' }}button{{ modifier ? ' ' ~ modifier : '' }}" {{ disabled? 'disabled': '' }}>{{ text }}</button>
```
A sample for this template might define variables for the text and modifier variables of this template that would show us how it's rendered under a specific set of conditions.  Here's an example:
```yaml
# button.yml
...
samples: # Sets of variables passed to the rendering engine.
    Primary: # The name of the first sample.
        text: Primary # a variable that means something to the template.
        modifier: primary # a variable that means something to the template.
    Secondary: # The name of the second sample.
        text: Secondary # a variable that means something to the template.
        modifier: secondary # a variable that means something to the template.
```

### Variables

Sample variables can also be nested.  For example, if the button template was rewritten to expect an object `content` such as `{{content.prefix}} - {{ content.text }}, you might use the following in a sample:
```twig
# button.yml
...
samples: # Sets of variables passed to the rendering engine.
    HelloWorld:
        content: 
            prefix: Hello
            text: World
```

### Expressions

So far, all of our variable values have been simple.  If you need something more complex, you can use an expression.  Expressions are powered by [Symfony Expression Language](https://symfony.com/doc/current/components/expression_language.html), and are provided by extensions.  Expressions are strings that are prefixed by `~`.  As an example, the core extension provides a `sample` expression that can be used to render a sample from a different template as a variable in the current template.  You would use it like this:
```yaml
# button.yml
...
samples:
  Help:
    content:
      prefix: ~sample('some/template.twig#HelpIcon')
      text: Ask for help!
```
This would render `some/template.twig` with that template's `HelpIcon` sample as the prefix, and it would be available in the current template as content.prefix. [Full list of expressions](expressions.md)

## Metadata Locations

Some extensions allow you to store component metadata inside of the template files.  For the `Twig` and `Drupal` extensions, you can write the metadata inside of the `componentinfo` block.  This is great for helping your templates become more self-documenting.  Here's an example:

```twig
# button.twig
{% if false %}{%block componentinfo %}
name: Button
description: An HTML Button
samples:
  Primary:
    text: Primary
    modifier: primary
{%endblock%}
... The rest of the Twig template. 
```

**Important**: If you use in-template metadata, be sure to guard the Twig block (`{%if false%}`) so it doesn't get printed when your template is rendered.