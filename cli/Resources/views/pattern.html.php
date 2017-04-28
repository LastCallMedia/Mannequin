<html>
<head>
  <title><?php print $view->escape($rendered->getPattern()->getName()); ?></title>
</head>
<body>
<?php print $view->render('partials/pattern-header', [
  'pattern' => $rendered->getPattern(),
  'generator' => $generator,
]); ?>
<?php print $rendered->getMarkup(); ?>
</body>
</html>