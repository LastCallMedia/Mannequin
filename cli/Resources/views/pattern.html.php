<?php $view->extend('html'); ?>
<?php $view['slots']->set('title', $view->escape($pattern->getName())); ?>
<?php print $view->render('partials/pattern-single', [
  'pattern' => $pattern,
]); ?>