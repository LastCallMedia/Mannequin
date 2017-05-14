<?php $view->extend('partials/html'); ?>
<?php $view['slots']->set('title', $view->escape($title)); ?>
<?php $view['slots']->start('styles'); ?>
  <?php foreach($rendered->getStyles() as $style) : ?>
    <link rel="stylesheet" href="<?php print $style; ?>" />
  <?php endforeach; ?>
<?php $view['slots']->stop(); ?>
<?php print $rendered->getMarkup(); ?>