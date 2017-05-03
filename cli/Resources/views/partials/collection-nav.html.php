<nav>
  <ul class="vertical menu" data-magellan>
    <?php foreach($collection->getPatterns() as $pattern) : ?>
      <li>
        <a href="#pattern-<?php print $view->escape($pattern->getId()); ?>">
          <?php print $view->escape($pattern->getName()); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

</nav>