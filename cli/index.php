<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use LastCall\Patterns\Cli\Controller\PatternController;
use Symfony\Component\HttpFoundation\Response;

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

$app->get('/', 'patterns.controller:indexAction');
$app->get('/patterns/{id}', 'patterns.controller:patternAction')
  ->bind('view_pattern');

$app->run();