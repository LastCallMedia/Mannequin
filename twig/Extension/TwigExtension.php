<?php


namespace LastCall\Mannequin\Twig\Extension;


use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Core\Metadata\ChainMetadataFactory;
use LastCall\Mannequin\Core\Metadata\MatchingPatternMetadataFactory;
use LastCall\Mannequin\Core\Metadata\YamlFileMetadataFactory;
use LastCall\Mannequin\Twig\Discovery\TwigFileDiscovery;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use LastCall\Mannequin\Twig\Metadata\TwigInlineMetadataFactory;
use LastCall\Mannequin\Twig\Parser\TwigParser;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class TwigExtension extends AbstractExtension {

  public function __construct(array $config = []) {
    $config += [
      'paths' => [],
      'twig' => function() {
        $cache_dir = $this->getConfig()->getCacheDir().DIRECTORY_SEPARATOR.'twig';
        $loader = new \Twig_Loader_Filesystem($this['paths']);
        return new \Twig_Environment($loader, [
          'cache' => $cache_dir,
          'auto_reload' => TRUE,
        ]);
      },
      'finder' => function() {
        return Finder::create()
          ->files()
          ->in($this['paths']);
      }
    ];
    parent::__construct($config);
    $this['discovery'] = function() {
      return new TwigFileDiscovery($this['twig']->getLoader(), $this['finder'], $this->getConfig()->getDispatcher());
    };
  }

  public function getDiscoverers(): array {
    return [ $this['discovery'] ];
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderers(): array {
    $config = $this->getConfig();
    return [new TwigEngine($this['twig'], $config->getVariableResolver(), $config->getStyles(), $config->getScripts())];
  }

  public function attachToDispatcher(EventDispatcherInterface $dispatcher) {
    $dispatcher->addSubscriber(new InlineTwigYamlMetadataSubscriber($this['twig']));
  }
}