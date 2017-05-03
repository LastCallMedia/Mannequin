<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($page_title)); ?>
<?php $view['slots']->set('page_title', $view->escape($page_title)); ?>
<?php print $view->render('partials/pattern', [
  'pattern' => $pattern,
]); ?>