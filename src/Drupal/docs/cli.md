---
title: CLI
description: How to run Mannequin from the command line.
---

Mannequin is primarily a command line application.  A full, current list of options and commands can be obtained for your installation by running Mannequin with no additional command.  Individual commands can be further inspected using `vendor/bin/mannequin help COMMAND`, where `COMMAND` is the name of the command you want more information on.
 
## Global Options

`--config`, `-c` (.mannequin.php): Specify an alternative config file.  Defaults to .mannequin.php.

`--debug`, `-d` (false): Causes exceptions to be shown in the browser when visiting a page that throws an error.

`-v`: Trigger verbose mode to increase the amount of output that is displayed.  This can be useful to help debug issues.  Verbosity can be increased by using the `-vv` or `-vvv` flags.

## Commands

### start
**Start a development server to view components in the browser.**  After the command, you can optionally specify an IP and port to bind to.

**Examples**
```bash
# Start a development server on 0.0.0.0:8000
> vendor/bin/mannequin start
# Start a development server on 127.0.0.1:8001
> vendor/bin/mannequin start 127.0.0.1:8001
```

### snapshot
**Capture all known components in static HTML format and save to a directory.**

**Options:**

`--output`, `-o`: The directory to save the static output into.  Defaults to `./mannequin`.

**Examples**
```bash
# Snapshot into ./mannequin.
> vendor/bin/mannequin snapshot
# Snapshot into /tmp/static
> vendor/bin/mannequin snapshot --output=/tmp/static
```

### debug
**Output a YAML manifest of your components.** This command can be useful to get a high level view of all of your components at once, or to include in bug reports.

**Examples**
```bash
> vendor/bin/mannequin debug
```
