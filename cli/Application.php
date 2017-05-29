<?php

namespace LastCall\Mannequin\Cli;

use LastCall\Mannequin\Cli\Command\RenderCommand;
use LastCall\Mannequin\Cli\Command\ServerCommand;
use LastCall\Mannequin\Cli\Controller\RenderController;
use LastCall\Mannequin\Cli\File\ExtensionMimeTypeGuesser;
use LastCall\Mannequin\Cli\Helper\ConfigHelper;
use LastCall\Mannequin\Cli\Ui\UiRenderer;
use LastCall\Mannequin\Cli\Ui\UiWriter;
use LastCall\Mannequin\Core\ConfigInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;


class Application extends \Silex\Application {

  const APP_NAME = 'Mannequin';
  const APP_VERSION = '0.0.0';

  public function __construct(array $values = []) {
    parent::__construct($values + ['debug' => TRUE]);
    $this['console'] = function() {
      $app = new ConsoleApplication(self::APP_NAME, self::APP_VERSION);
      $app->getHelperSet()->set(new ConfigHelper());
      $app->addCommands($this['commands']);
      return $app;
    };
    $this['config'] = function() {
      $filename = $this['config_file'];
      if(!file_exists($filename)) {
        throw new \RuntimeException(sprintf('Expected config in %s, but the file does not exist.', $filename));
      }
      $config = require_once $filename;
      if(!$config instanceof  ConfigInterface) {
        throw new \RuntimeException('Config was not returned or not an instance of Config.');
      }
      return $config;
    };
    $this['commands'] = function() {
      return [
        new RenderCommand('render', $this['ui.writer'], $this['config']),
        new ServerCommand('server', $this['config_file'], $this['autoload_path']),
      ];
    };
    $this['template_engine'] = function() {
      $parser = new TemplateNameParser();
      $loader = new FilesystemLoader([__DIR__.'/Resources/ui/%name%']);
      return new PhpEngine($parser, $loader);
    };
    $this['ui.writer'] = function() {
      $this->boot();
      $this->flush();
      return new UiWriter($this['ui.renderer'], $this['url_generator']);
    };
    $this['ui.renderer'] = function() {
      return new UiRenderer($this['config']->getRenderer(), $this['template_engine'], $this['config']->getLabeller());
    };

    $this->register(new ServiceControllerServiceProvider());
    $this['controller.render'] = function() {
      $generator = $this['url_generator'];
      $collection = $this['config']->getCollection();
      return new RenderController($collection, $this['ui.renderer'], $generator);
    };

    $this->get('/', 'controller.render:indexAction');
    $this->get('/manifest.json', 'controller.render:manifestAction')->bind('manifest');
    $this->get('/_render/{pattern}', 'controller.render:renderAction')->bind('pattern_render');
    $this->get('/_source/{pattern}', 'controller.render:sourceAction')->bind('pattern_source');
    $this->get('/{name}', 'controller.render:staticAction')->assert('name','.+');
  }

  public function boot() {
    // Guess file extensions for CSS and JS files.
    MimeTypeGuesser::getInstance()->register(new ExtensionMimeTypeGuesser());
    return parent::boot();
  }

  public function getConsole() {
    return $this['console'];
  }
}