<?php foreach($collection as $pattern) : ?>
<?php print $view->render('partials/pattern-single', [
  'pattern' => $pattern,
]); ?>
<?php endforeach; ?>