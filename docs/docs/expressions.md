---
title: Expressions
description: Learn about sample variable expressions
---
As described in [Components](../docs/components.md), Sample variables can use more complex values by using Expressions.  Any value that is prefixed by `~` is treated as an expression.  In the following example, the "text" variable uses the `rendered` function to mark some HTML as safe so it is not escaped during rendering:
 
```yaml
# button.yml
...
samples:
  HeadingButton:
    text: ~rendered('<h3>This is HTML</h3>')
```

Which expression functions are available to you depends on which extensions you are using.  Here are all of the expression functions provided by Mannequin:

| Function | Extension | Example | Description |
| -------- | --------- | ------- | ----------- |
| `rendered` | Core | ~rendered('<i></i>') | Wraps an HTML string in a `Rendered` object that the rendering engine so the rendering engine knows not to escape it. |
| `asset` | Core | ~asset('img/logo.png') | Formulates a path to a local asset. |
| `sample` | Core | ~sample('button.twig#HeadingButton') | Renders another component sample in the current component. |
| `attributes` | Drupal | ~attributes({class: ['foo']}) | Creates a Drupal Core `Attributes` object. |
