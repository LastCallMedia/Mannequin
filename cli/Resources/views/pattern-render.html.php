<?php $view->extend('html'); ?>
<?php $view['slots']->set('title', $view->escape($rendered->getPattern()->getName())); ?>
<?php print $rendered->getMarkup(); ?>