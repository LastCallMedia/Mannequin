---
title: YAML Reference
description: A complete reference to YAML files for the Mannequin HTML extension.
---
Component information is stored in YAML files that live alongside the HTML files.

### Name

The component name is specified under the `name` key.  Example:
```yaml
name: Super Button
```

### Group

The component's group determines where it displays in the UI.  The group can be nested using the `>` character.  It is specified beneath the `group` key.  Example:
```yaml
group: Atoms>Buttons
```