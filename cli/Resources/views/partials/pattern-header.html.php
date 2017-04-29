<h1><?php print $view->escape($pattern->getName()); ?></h1>
<div class="pattern--tags">
  <?php foreach($pattern->getTags() as $type => $values) : ?>
    <div class="pattern--tags--badge">
      <span class="type"><?php print $view->escape($type); ?>:</span>
      <span class="values">
        <?php foreach($values as $value) : ?>
          <?php $href = $view['url']->generate('collection_index', ['collection' => sprintf('%s:%s', $type, $value)]); ?>
          <a href="<?php print $href; ?>"><?php print $view->escape($value); ?></a>
        <?php endforeach; ?>
      </span>
    </div>
  <?php endforeach; ?>
</div>