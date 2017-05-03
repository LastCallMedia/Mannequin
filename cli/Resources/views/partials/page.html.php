<?php $view->extend('partials/html'); ?>
<?php $view['slots']->start('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css" />
<?php $view['slots']->stop(); ?>
<?php $view['slots']->start('scripts'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.min.js"></script>
<?php $view['slots']->stop(); ?>
<div class="row">
  <div class="columns small-12">
    <?php $view['slots']->output('_content'); ?>
  </div>
</div>