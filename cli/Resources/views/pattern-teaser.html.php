<div class="pattern" id="<?php print $id; ?>" data-magellan-target="<?php print $id; ?>">
  <h2 class="pattern--title"><?php print $view->escape($title); ?></h2>
  <div class="pattern--tags">
    <?php foreach($tags as $tag) :?>
      <a href="<?php print $tag['url']; ?>" class="label"><?php print $view->escape($tag['title']); ?></a>
    <?php endforeach; ?>
  </div>
  <iframe frameborder="0" src="<?php print $rendered_url; ?>"></iframe>
</div>