<?php

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Console\Application as ConsoleApplication;
use LastCall\Mannequin\Core\Console\Command\RenderCommand;
use LastCall\Mannequin\Core\Console\Command\ServerCommand;
use LastCall\Mannequin\Core\MimeType\ExtensionMimeTypeGuesser;
use LastCall\Mannequin\Core\Ui\Controller\ManifestController;
use LastCall\Mannequin\Core\Ui\Controller\RenderController;
use LastCall\Mannequin\Core\Ui\Controller\UiController;
use LastCall\Mannequin\Core\Ui\HtmlDecorator;
use LastCall\Mannequin\Core\Ui\Manifester;
use LastCall\Mannequin\Core\Ui\UiRenderer;
use LastCall\Mannequin\Core\Ui\UiWriter;
use LastCall\Mannequin\Ui\Ui;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;


class Application extends \Silex\Application {

  const APP_NAME = 'Mannequin';
  const APP_VERSION = '0.0.0';

  public function __construct(array $values = []) {
    parent::__construct($values);
    $this['console'] = function() {
      $app = new ConsoleApplication(self::APP_NAME, self::APP_VERSION);
      $app->setDispatcher($this['dispatcher']);
      $config = $this->getConfig();
      $app->addCommands([
        new RenderCommand('render', $this['manifester'], $this['config']->getRenderer(), $config->getCollection(), $this['ui.decorator'], $this['ui'], $config->getAssetMappings()),
        new ServerCommand('server', $this['config_file'], $this['autoload_path'], $this['debug']),
      ]);
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
    $this['manifester'] = function() {
      $this->flush();
      return new Manifester($this['url_generator']);
    };
    $this['ui'] = function() {
      $config = $this['config'];
      if(isset($config['ui'])) {
        return $config['ui'];
      }
      return new Ui();
    };
    $this['ui.decorator'] = function() {
      return new HtmlDecorator();
    };

    $this->register(new ServiceControllerServiceProvider());
    $this['controller.ui'] = function() {
      return new UiController($this['ui']);
    };
    $this['controller.manifest'] = function() {
      return new ManifestController($this['manifester'], $this['config']->getCollection());
    };
    $this['controller.render'] = function() {
      $collection = $this['config']->getCollection();
      return new RenderController($collection, $this['config']->getRenderer(), $this['ui.decorator']);
    };

    $this->get('/manifest.json', 'controller.manifest:getManifestAction')->bind('manifest');
    $this->get('/m-render/{pattern}/{set}.html', 'controller.render:renderAction')->bind('pattern_render');
    $this->get('/m-source/raw/{pattern}.txt', 'controller.render:renderSourceAction')->bind('pattern_render_source_raw');
    $this->get('/m-source/html/{pattern}/{set}.txt', 'controller.render:renderRawAction')->bind('pattern_render_raw');
    $this->match('/{name}', 'controller.ui:staticAction')
      ->value('name', 'index.html')
      ->assert('name','.+');
  }

  public function boot() {
    // Guess file extensions for CSS and JS files.
    MimeTypeGuesser::getInstance()->register(new ExtensionMimeTypeGuesser());
    return parent::boot();
  }

  /**
   * @return \LastCall\Mannequin\Core\ConfigInterface
   */
  protected function getConfig() {
    return $this['config'];
  }

  public function getConsole() {
    return $this['console'];
  }
}