<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use LastCall\Patterns\Cli\Controller\PatternController;

$app = new Application(['debug' => TRUE]);
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

$app['config'] = function() {
  return require __DIR__.'/../.patterns.php';
};
$app['collection'] = function() use ($app) {
  return $app['config']->getCollection();
};
$app['patterns.controller'] = function() use ($app) {
  $config = $app['config'];
  return new PatternController($config->getCollection(), $config->getRenderer(), $config->getUi(), $app['url_generator']);
};

$app
  ->get('/', 'patterns.controller:indexAction')
  ->bind('pattern_index');
$app
  ->get('/patterns/{pattern}', 'patterns.controller:patternAction')
  ->bind('pattern_view')
  ->convert('pattern', 'patterns.controller:convertPattern');

$app
  ->get('/tags/{tag}', 'patterns.controller:tagAction')
  ->bind('pattern_tag_view')
  ->convert('tag', 'patterns.controller:convertTag')
  ->assert('tag', '.+\:.+');



$app->run();