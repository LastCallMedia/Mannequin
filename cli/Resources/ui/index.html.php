<?php
$contents=<<<'EOD'
<?php
$valid_urls = [
  '/',
  '/index.html',
  '/index.php',
];
if(in_array($_SERVER['REQUEST_URI'], $valid_urls) && file_exists(__DIR__.'/index.html')) {
  header("HTTP/1.0 200 OK");
  include __DIR__.'/index.html';
  exit();
}
header("HTTP/1.0 404 Not Found");
print '404';
EOD;
print $contents;