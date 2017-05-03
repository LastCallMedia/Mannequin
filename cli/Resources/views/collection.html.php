<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($collection->getName())); ?>
<?php print $view->render('partials/collection-list', [
  'collection' => $collection,
]); ?>
