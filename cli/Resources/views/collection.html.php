<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($page_title)); ?>
<?php $view['slots']->set('page_title', $view->escape($page_title)); ?>
<?php $view['slots']->start('page_nav'); ?>
  <?php print $view->render('partials/collection-nav', [
    'collection' => $collection,
  ]); ?>
<?php $view['slots']->stop(); ?>
<?php print $view->render('partials/collection-list', [
  'collection' => $collection,
]); ?>
