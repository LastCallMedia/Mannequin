<?php
$titles = [];
$parent = $collection;
while($parent = $parent->getParent()) {
  if($parent->getId() === 'default') {
    $href = $view['url']->generate('pattern_index');
  }
  else {
    $href = $view['url']->generate('collection_index', ['collection' => $parent->getId()]);
  }
  $titles[] = sprintf('<a href="%s">%s</a>', $href, $view->escape($parent->getName()));
}
$titles[] = $view->escape($collection->getName());
?>

<h1><?php print implode(' &raquo; ', $titles); ?></h1>