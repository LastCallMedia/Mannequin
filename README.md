Patterns
========

A library for generating a style guide from templates. [View a demo](https://demo.mannequin.io), or [read the docs](https://mannequin.io).

Development
-----------

To work on this project, you need:

- PHP >= 7.0
- NodeJS >= 6.0

In development, there are two servers that need to be started to see your changes immediately:

- (from the /ui directory) `npm run start`
- (from the root directory) `core/bin/cli --ui-server=http://localhost:3000`

Once these are up and running, you should be able to visit http://localhost:8000 in your browser, and see changes to both PHP and React code as you reload the page.

Deployment/Packaging
--------------------

This repository is split using [splitsh-lite](https://github.com/splitsh/lite) running inside of the [derusse/docker-gitsplit](https://github.com/jderusse/docker-gitsplit) Docker container.  Pushes to the master branch, as well as pushes of any tags, will be moved to the downstream repositories.