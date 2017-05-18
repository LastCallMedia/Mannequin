<html>
<head>
  <title><?php print $view->escape($title); ?></title>
  <?php foreach($styles as $style): ?>
    <link rel="stylesheet" href="<?php print $style; ?>">
  <?php endforeach; ?>
</head>
<body>
  <?php print $markup; ?>
  <?php foreach($scripts as $script) : ?>
    <script type="text/javascript" src="<?php print $script; ?>"></script>
  <?php endforeach; ?>
</body>
</html>