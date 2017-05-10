<?php

require_once __DIR__.'/../vendor/autoload.php';

use LastCall\Patterns\Cli\Helper\ConfigHelper;
use LastCall\Patterns\Cli\Controller\PatternController;
use LastCall\Patterns\Cli\Templating\Helper\UrlHelper;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Silex\Application;

$app = new Application(['debug' => TRUE]);
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \LastCall\Patterns\Cli\ServiceProvider\TemplatingServiceProvider());

$app['templating.directories'] = [__DIR__.'/Resources/views/%name%'];
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
  /** @var \LastCall\Patterns\Core\Config $config */
  $config = $app['config'];
  return new PatternController($config->getCollection(), $config->getRenderer(), $config->getLabeller(), $app['templating'], $app['url_generator']);
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
  ->bind('collection_pattern_view');

$app
  ->get('/patterns/{pattern}', 'patterns.controller:patternAction')
  ->bind('pattern_view')
  ->convert('pattern', 'patterns.controller:convertPattern');

$app
  ->get('/render/{pattern}', 'patterns.controller:renderAction')
  ->bind('pattern_render')
  ->convert('pattern', 'patterns.controller:convertPattern');

$app->run();