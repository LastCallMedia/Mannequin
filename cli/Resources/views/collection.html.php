<?php $view->extend('partials/page'); ?>
<?php $view['slots']->set('title', $view->escape($page_title)); ?>
<?php $view['slots']->set('page_title', $view->escape($page_title)); ?>
<?php $view['slots']->set('navigation', $navigation); ?>
<?php $view['slots']->set('breadcrumb', $view->render('partials/breadcrumb', [
  'parts' => $breadcrumb,
])); ?>
<?php $view['slots']->start('page_nav'); ?>
  <nav>
    <ul class="vertical menu" data-magellan>
      <?php foreach($patterns_nav as $nav) : ?>
        <li>
          <a href="<?php print $nav['url']; ?>"><?php print $view->escape($nav['title']); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
<?php $view['slots']->stop(); ?>
<?php foreach($patterns as $pattern) : ?>
  <?php print $pattern; ?>
<?php endforeach; ?>
