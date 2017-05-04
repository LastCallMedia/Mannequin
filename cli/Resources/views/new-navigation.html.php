<ul class="vertical menu medium-horizontal" data-responsive-menu="drilldown medium-dropdown">
  <?php foreach($tree as $leaf): ?>
    <li>
      <a href="<?php print $leaf['url']; ?>"><?php print $leaf['title']; ?></a>
      <?php if(!empty($leaf['below'])) : ?>
        <ul class="menu">
          <?php foreach($leaf['below'] as $l2Leaf): ?>
            <li>
              <a href="<?php print $l2Leaf['url']; ?>"><?php print $l2Leaf['title']; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>