<html>
<head>

</head>
<body>
<?php print $view->render('partials/collection-title', [
  'collection' => $collection,
]); ?>
<?php print $view->render('partials/collection-list', [
  'collection' => $collection,
]); ?>
</body>
</html>