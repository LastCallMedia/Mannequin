<html>
<head>
  <title><?php $view['slots']->output('title', 'Home'); ?></title>
  <?php $view['slots']->output('styles'); ?>
</head>
<body>
  <?php $view['slots']->output('_content'); ?>
  <?php $view['slots']->output('scripts'); ?>
</body>
</html>