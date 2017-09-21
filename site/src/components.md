---
title: Components
---
A pattern is a bit of code that generates HTML markup.  Most commonly, Mannequin patterns are template files living in your codebase.  Templates become patterns in Mannequin when:

1.  You tell a Mannequin [discoverer](extension.md#Discoverer) where to find them.
2.  You have YAML describing the pattern.

For example, imagine that you have the following [Twig](https://twig.sensiolabs.org) template:
```twig
{# card.twig #}
<div class="card">
  <img src="{img_src}" />
  <h3>{title}</h3>
</div>
```

Once you tell the Twig Extension how to find this template, you can add a card.yml file that lives alongside the template that describes it.
```yaml
name: Simple Card
_group: Molecule>Containers
```

This YAML gives the pattern a name and a group that it belongs to.

Variants
--------
A variant is a version of the pattern that is rendered with specific variables. Any pattern that can have variables can have variants.  Using our card example, you might create variants that show off how it will look with a short title, and how it will look with a long title.  We need to describe these variants in YAML in order to specify values for `title` and `img_src`:

```yaml
variants:
  Terse:
    title: Lorem Ipsum
    img_src: //placehold.it/480x480.jpg
  Verbose:
    title: Lorem ipsum dolor sit amet, consectetur adipiscing elit
    img_src: //placehold.it/480x480.jpg
```
This creates two variants for our card template.

YAML Reference
--------------
```yaml
name: # The display name of the pattern.
_group: # The grouping to place the pattern in.  Use a > character to nest the group.
_description: # The long form description of the pattern.  Add implementation instructions, or anything else you need here.
variants: # An associative describing the sets of variables you would like to use.
  Variant1: # This will be the name of the variant.  This is an associative array describing the VALUES for the variables.
    _name: # A name for the description, if you want to use something other than the key you used above.
    _description: # A textual description for this variant.
    variable_1...: # Declare values for as many variables as you like here.

```



