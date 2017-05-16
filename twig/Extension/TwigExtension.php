<?php


namespace LastCall\Patterns\Twig\Extension;


use LastCall\Patterns\Core\Extension\AbstractExtension;
use LastCall\Patterns\Core\Metadata\YamlFileMetadataParser;
use LastCall\Patterns\Twig\Discovery\TwigFileDiscovery;
use LastCall\Patterns\Twig\Metadata\TwigInlineMetadataParser;
use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Render\TwigRenderer;
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
      return new TwigInlineMetadataParser($this['twig'], $config->getVariableFactory());
    };
    $this['discovery'] = function() {
      $config = $this->getConfig();
      return new TwigFileDiscovery($this['loader'], $this['finder'], $config->getVariableFactory(), $this['metadata_parser']);
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
}