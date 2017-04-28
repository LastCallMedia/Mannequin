<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use LastCall\Patterns\Cli\Controller\PatternController;

$app = new Application(['debug' => TRUE]);
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \LastCall\Patterns\Cli\ServiceProvider\TemplatingServiceProvider());

$app['templating.directories'] = [__DIR__.'/Resources/views/%name%'];
$app['config'] = function() {
  return require __DIR__.'/../.patterns.php';
};
$app['collection'] = function() use ($app) {
  return $app['config']->getCollection();
};
$app['patterns.controller'] = function() use ($app) {
  $config = $app['config'];
  return new PatternController($config->getCollection(), $config->getRenderer(), $app['templating'], $app['url_generator']);
};

$app
  ->get('/', 'patterns.controller:rootAction')
  ->bind('pattern_index');

$app
  ->get('/collection/{collection}', 'patterns.controller:collectionAction')
  ->convert('collection', 'patterns.controller:convertCollection')
  ->bind('collection_index');

$app
  ->get('/patterns/{pattern}', 'patterns.controller:patternAction')
  ->bind('pattern_view')
  ->convert('pattern', 'patterns.controller:convertPattern');


$app->run();