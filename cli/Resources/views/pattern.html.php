<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($pattern->getName())); ?>
<?php print $view->render('partials/pattern-single', [
  'pattern' => $pattern,
]); ?>