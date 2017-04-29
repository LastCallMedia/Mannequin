<html>
<head>
  <title><?php print $view->escape($pattern->getName()); ?></title>
</head>
<body>
<?php print $view->render('partials/pattern-single', [
  'pattern' => $pattern,
]); ?>
</body>
</html>