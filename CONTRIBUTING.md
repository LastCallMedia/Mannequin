# How to contribute

Welcome, and thanks for contributing to Mannequin!

## Reporting Bugs

It's very helpful to include the following information when reporting a bug:

* PHP Version
* Mannequin version
* Extensions you are using
* Console output of any commands that aren't doing what you expected.

## Pull Requests

Pull Requests will be run through a comprehensive suite of automated tests.  Please be sure that you include tests for any changes that are possible to test.  If you need help writing tests, let us know!

Development Setup:
------------------

To work on this project, you need:

- PHP >= 7.0
- NodeJS >= 6.0

In development, there are two servers that need to be started to see your changes immediately:

* From the /ui directory, run: `yarn start`.  This will start the front end server running on port 3000.
* Next, in .mannequin.php, use the LocalDevelopmentUI as follows:
  ```php
  use LastCall\Mannequin\Core\MannequinConfig;
  use LastCall\Mannequin\Core\Ui\LocalDevelopmentUi;
  
  $config = MannequinConfig::create([
    'ui' => new LocalDevelopmentUi('http://127.0.0.1:3000')
  ])
  ...
  ```
* Finally, run: `src/Core/bin/mannequin start *:8000`.  This will start the backend server, and you should be able to visit http://localhost:8000 in your browser, and see changes to both PHP and React code as you reload the page.

Deployment/Packaging
--------------------

This repository is split using [splitsh-lite](https://github.com/splitsh/lite) running inside of the [derusse/docker-gitsplit](https://github.com/jderusse/docker-gitsplit) Docker container.  Pushes to the master branch, as well as pushes of any tags, will be moved to the downstream repositories.