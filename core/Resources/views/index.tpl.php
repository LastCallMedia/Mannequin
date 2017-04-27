<html>
<head>

</head>
<body>
<h1><?php print $title; ?></h1>
<ul>
  <?php foreach($patterns as $pattern): ?>
    <?php $url = $generator->generate('view_pattern', ['id' => $pattern->getId()]); ?>
    <li><a href="<?php print $url; ?>"><?php print $view->escape($pattern->getName()); ?></a></li>
  <?php endforeach; ?>
</ul>
</body>
</html>