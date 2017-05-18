<?php

require_once __DIR__.'/../vendor/autoload.php';

use LastCall\Mannequin\Cli\Helper\ConfigHelper;
use LastCall\Mannequin\Cli\Controller\PatternController;
use LastCall\Mannequin\Cli\Controller\AssetController;
use LastCall\Mannequin\Cli\Templating\Helper\UrlHelper;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Silex\Application;

$autoload_file = FALSE;
foreach (array(__DIR__ . '/../../../autoload.php', __DIR__ . '/../../vendor/autoload.php', __DIR__ . '/../vendor/autoload.php') as $file) {
  if (file_exists($file)) {
    $autoload_file = $file;
  }
}
if($autoload_file) {
  $autoloader = require $autoload_file;
}
else {
  throw new \Exception('Application is not installed.');
}

$app = new Application([
  'debug' => TRUE,
  'autoloader' => $autoloader,
]);
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \LastCall\Mannequin\Cli\ServiceProvider\TemplatingServiceProvider([]), [
  'templating.directories' => [__DIR__.'/Resources/views/%name%']
]);

$app['config.filename'] = function() {
  return ($filename = getenv('PATTERN_CONFIG')) ? $filename : '.patterns.php';
};
$app['config'] = function() use ($app) {
  return (new ConfigHelper())->getConfig($app['config.filename']);
};
$app['collection'] = function() use ($app) {
  return $app['config']->getCollection();
};
$app['patterns.controller'] = function() use ($app) {
  /** @var \LastCall\Mannequin\Core\Config $config */
  $config = $app['config'];
  return new PatternController($config->getCollection(), $config->getRenderer(), $config->getLabeller(), $app['templating'], $app['url_generator']);
};
$app['asset.controller'] = function() use ($app) {
  /** @var \LastCall\Mannequin\Core\Config $config */
  $config = $app['config'];
  return new AssetController($config->getAssetMappings());
};

$app->extend('templating.helpers', function(array $helpers) use ($app) {
  $helpers[] = new UrlHelper($app['url_generator']);
  $helpers[] = new SlotsHelper();
  return $helpers;
});

$app
  ->get('/', 'patterns.controller:rootAction')
  ->bind('pattern_index');

$app
  ->get('/collection/{collection}', 'patterns.controller:collectionAction')
  ->convert('collection', 'patterns.controller:convertCollection')
  ->bind('collection_index');

$app
  ->get('/collection/{collection}/{pattern}', 'patterns.controller:collectionPatternAction')
  ->convert('collection', 'patterns.controller:convertCollection')
  ->convert('pattern', 'patterns.controller:convertPattern')
  ->assert('pattern', '.+')
  ->bind('collection_pattern_view');

$app
  ->get('/patterns/{pattern}', 'patterns.controller:patternAction')
  ->bind('pattern_view')
  ->convert('pattern', 'patterns.controller:convertPattern')
  ->assert('pattern', '.+');

$app
  ->get('/render/{pattern}', 'patterns.controller:renderAction')
  ->bind('pattern_render')
  ->assert('pattern', '.+')
  ->convert('pattern', 'patterns.controller:convertPattern');

// Match asset paths.
$app->match('{url}', 'asset.controller:getAssetAction')
  ->assert('url', '.*');


$app->run();