<div class="pattern">
  <h2 class="pattern--title"><?php print $view->escape($pattern->getName()); ?></h2>
  <iframe frameborder="0" src="<?php print $view['url']->generate('pattern_render', ['pattern' => $pattern->getId()]); ?>"></iframe>
</div>