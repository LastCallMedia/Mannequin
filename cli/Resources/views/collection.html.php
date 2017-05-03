<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($collection->getName())); ?>
<?php $view['slots']->start('page_nav'); ?>
  <?php print $view->render('partials/collection-nav', [
    'collection' => $collection,
  ]); ?>
<?php $view['slots']->stop(); ?>
<?php print $view->render('partials/collection-list', [
  'collection' => $collection,
]); ?>
