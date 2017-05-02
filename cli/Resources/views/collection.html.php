<?php $view->extend('html'); ?>
<?php $view['slots']->set('title', $collection->getName()); ?>
<?php print $view->render('partials/collection-list', [
  'collection' => $collection,
]); ?>