<?php $view->extend('partials/html'); ?>
<?php $view['slots']->start('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css" />
<?php $view['slots']->stop(); ?>
<?php $view['slots']->start('scripts'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.min.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery(document).foundation();
  });
</script>
<?php $view['slots']->stop(); ?>
<div class="top-bar">
  <div class="top-bar-left">
    <ul class="menu"><li class="menu-text">Patterns</li></ul>
  </div>
  <div class="top-bar-right">
    <?php print $view->render('partials/navigation', ['collection' => $root]); ?>
  </div>
</div>
<div class="row">
  <div class="columns small-12">
    <?php $view['slots']->output('_content'); ?>
  </div>
</div>