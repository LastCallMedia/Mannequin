<?php
$groups = [
  'atoms' => $collection->withTag('type', 'atom', 'Atoms'),
  'molecules' => $collection->withTag('type', 'molecule', 'Molecules'),
  'elements' => $collection->withTag('type', 'element', 'Elements'),
];
?>
<ul class="vertical menu medium-horizontal" data-responsive-menu="drilldown medium-dropdown">
  <?php foreach($groups as $group) : ?>
    <li>
      <a href="<?php print $view['url']->generate('collection_index', ['collection' => $group->getId()]); ?>">
        <?php print $view->escape($group->getName()); ?>
      </a>
      <?php if($group->count() > 0) : ?>
        <ul class="vertical menu">
          <?php foreach($group->getPatterns() as $pattern) : ?>
            <li>
              <a href="<?php print $view['url']->generate('pattern_view', ['pattern' => $pattern->getId()]); ?>">
                <?php print $view->escape($pattern->getName()); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>