<?php $view->extend('partials/html'); ?>
<?php $view['slots']->set('title', $view->escape($title)); ?>
<?php $view['slots']->start('styles'); ?>
  <?php foreach($styles as $style) : ?>
    <link rel="stylesheet" href="<?php print $style; ?>" />
  <?php endforeach; ?>
<?php $view['slots']->stop(); ?>
<?php print $markup; ?>
<?php $view['slots']->start('scripts'); ?>
  <?php foreach($scripts as $script) : ?>
    <script type="text/javascript" src="<?php print $script; ?>"></script>
  <?php endforeach; ?>
<?php $view['slots']->stop(); ?>
