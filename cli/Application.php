<?php


namespace LastCall\Mannequin\Cli;

use LastCall\Mannequin\Cli\Command\RenderCommand;
use LastCall\Mannequin\Cli\Command\ServerCommand;
use LastCall\Mannequin\Cli\Helper\ConfigHelper;
use LastCall\Mannequin\Cli\Writer\UiWriter;
use Pimple\Container;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;


class Application extends Container {

  const APP_NAME = 'Mannequin';
  const APP_VERSION = '0.0.0';

  public function __construct(array $values = []) {
    parent::__construct($values);
    $this['console'] = function() {
      $app = new ConsoleApplication(self::APP_NAME, self::APP_VERSION);
      $app->getHelperSet()->set(new ConfigHelper());
      $app->addCommands($this['commands']);
      return $app;
    };
    $this['commands'] = function() {
      return [
        new RenderCommand('render', $this['ui.writer']),
        new ServerCommand('server', $this['ui.writer']),
      ];
    };
    $this['template_engine'] = function() {
      $parser = new TemplateNameParser();
      $loader = new FilesystemLoader([__DIR__.'/Resources/ui/%name%']);
      return new PhpEngine($parser, $loader);
    };
    $this['ui.writer'] = function() {
      return new UiWriter($this['template_engine']);
    };
  }

  public function getConsole() {
    return $this['console'];
  }
}