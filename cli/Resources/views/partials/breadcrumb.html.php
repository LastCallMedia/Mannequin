<ul class="breadcrumbs">
  <?php foreach($parts as $part) : ?>
    <li><a href="<?php print $part['url']; ?>"><?php print $part['title']; ?></a></li>
  <?php endforeach; ?>
</ul>