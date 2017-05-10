<?php


namespace LastCall\Patterns\Twig\Extension;


use LastCall\Patterns\Core\Extension\AbstractExtension;
use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Render\TwigRenderer;

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

  public function getParsers(): array {
    $config = $this->getConfig();
    return [new TwigParser($this['twig'], $config->getVariableFactory())];
  }

  public function getRenderers(): array {
    $config = $this->getConfig();
    return [new TwigRenderer($this['twig'], $config->getVariables(), $config->getStyles(), $config->getScripts())];
  }
}