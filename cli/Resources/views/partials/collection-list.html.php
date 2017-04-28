<ul>
  <?php foreach($collection as $pattern): ?>
    <?php $url = $generator->generate('pattern_view', ['pattern' => $pattern->getId()]); ?>
    <li><a href="<?php print $url; ?>"><?php print $view->escape($pattern->getName()); ?></a></li>
  <?php endforeach; ?>
</ul>