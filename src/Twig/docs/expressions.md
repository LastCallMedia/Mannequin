---
title: Expressions
description: Reference information for expression in Mannequin Drupal.
---
Sometimes, you want to pass in more complex values for your sample variables. To support that, Mannequin provides "expressions" that you can use in your component metadata.  Any variable that is prefixed by a `~` is treated as an expression.

## rendered

The `rendered` expression wraps markup strings in a special wrapper. Without wrapping them, they would be escaped by Twig when they are output.

```twig
{# @Component
... 
samples:
  MarkupExample:
    text: ~markup('<i class="icon icon-edit" title="Edit"></i>')
#}
<a>{{ text }}</a>
```

## asset

The `asset` expression formulates a path to a local file.  Asset paths are searched relative to the `.mannequin.php` file.

```twig
{# @Component
... 
samples:
  AssetExample:
    img_url: ~asset('img/logo.png')
#}
<img src="{{ img_url }}" />
```

## sample

The `sample` expression is a way of rendering a sample from another component in the current component.  It is a powerful way to preview how your components will come together.  It takes the name of a component, and the name of a sample defined within that component, separated by a "#".  Eg:

```twig
{# @Component
...
samples:
  SampleExample:
    cards:
      - ~sample('@mytheme/card.html.twig#Medium')
      - ~sample('@mytheme/card.html.twig#Large')
#}
<div class="card-grid">
  {% for card in cards %}
    {{ card }}
  {% endfor %}
</div>
```
