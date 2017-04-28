<html>
<head>

</head>
<body>
<?php print $view->render('partials/collection-title', [
  'collection' => $collection,
  'generator' => $generator,
]); ?>
<?php print $view->render('partials/collection-list', [
  'collection' => $collection,
  'generator' => $generator,
]); ?>
</body>
</html>