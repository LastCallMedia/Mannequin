<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($page_title)); ?>
<?php $view['slots']->set('page_title', $view->escape($page_title)); ?>
<?php $view['slots']->set('navigation', $navigation); ?>
<?php $view['slots']->set('breadcrumb', $view->render('partials/breadcrumb', [
  'parts' => $breadcrumb,
])); ?>
<div class="pattern--tags">
  <?php foreach($tags as $tag): ?>
    <a class="label" href="<?php print $tag['url']; ?>"><?php print $view->escape($tag['title']); ?></a>
  <?php endforeach; ?>
</div>
<iframe frameborder="0" src="<?php print $rendered_url; ?>"></iframe>