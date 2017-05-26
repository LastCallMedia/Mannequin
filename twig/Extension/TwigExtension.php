<?php


namespace LastCall\Mannequin\Twig\Extension;


use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Core\Metadata\ChainMetadataFactory;
use LastCall\Mannequin\Core\Metadata\MatchingPatternMetadataFactory;
use LastCall\Mannequin\Core\Metadata\YamlFileMetadataFactory;
use LastCall\Mannequin\Twig\Discovery\TwigFileDiscovery;
use LastCall\Mannequin\Twig\Metadata\TwigInlineMetadataFactory;
use LastCall\Mannequin\Twig\Parser\TwigParser;
use LastCall\Mannequin\Twig\Render\TwigRenderer;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

/**
 * @method addExtension(\Twig_ExtensionInterface $extension)
 * @method addFilter(\Twig_Filter $filter)
 * @method addFunction(\Twig_Function $function)
 * @method addTest(\Twig_Test $test)
 */
class TwigExtension extends AbstractExtension {

  protected static $proxiedMethods = [
    'addExtension',
    'addFilter',
    'addFunction',
    'addTest',
  ];


  public function __construct(array $config = []) {
    $config += [
      'paths' => [],
    ];
    parent::__construct($config);
    $this['finder'] = function() {
      return new Finder();
    };
    $this['loader'] = function() {
      return new \Twig_Loader_Filesystem($this['paths']);
    };
    $this['twig'] = function() {
      $cache_dir = $this->getConfig()->getCacheDir().DIRECTORY_SEPARATOR.'twig';
      return new \Twig_Environment($this['loader'], [
        'cache' => $cache_dir,
        'auto_reload' => TRUE,
      ]);
    };
    $this['metadata_parser'] = function() {
      $config = $this->getConfig();
      return new ChainMetadataFactory([
        new MatchingPatternMetadataFactory('/.*/', ['format' => 'twig']),
        new TwigInlineMetadataFactory($this['twig'], $config->getVariableFactory()),
      ]);
    };
    $this['discovery'] = function() {
      return new TwigFileDiscovery($this['twig']->getLoader(), $this['finder'], $this->getConfig()->getDispatcher());
    };
  }

  public function in($dirs) {
    return $this->extend('finder', function(Finder $finder) use ($dirs) {
      $finder->in($dirs);
      return $finder;
    });
    return $this;
  }

  public function __call($name, $arguments) {
    if(in_array($name, static::$proxiedMethods)) {
      $this->extend('twig', function(\Twig_Environment $twig) use ($name, $arguments) {
        $twig->{$name}(...$arguments);
        return $twig;
      });
      return TRUE;
    }
  }

  public function getDiscoverers(): array {
    return [ $this['discovery'] ];
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderers(): array {
    $config = $this->getConfig();
    return [new TwigRenderer($this['twig'], $config->getVariables(), $config->getStyles(), $config->getScripts())];
  }

  public function attachToDispatcher(EventDispatcherInterface $dispatcher) {
    $dispatcher->addSubscriber(new InlineTwigYamlMetadataSubscriber($this->getConfig()->getVariableFactory(), $this['twig']));
  }
}