<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($page_title)); ?>
<?php $view['slots']->set('page_title', $view->escape($page_title)); ?>
<?php $view['slots']->set('navigation', $navigation); ?>
<iframe frameborder="0" src="<?php print $rendered_url; ?>"></iframe>