<?php


namespace LastCall\Patterns\Twig\Extension;


use LastCall\Patterns\Core\Extension\AbstractExtension;
use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Render\TwigRenderer;

class TwigExtension extends AbstractExtension {

  private $paths = [];

  private $twig;

  public function __construct(array $config = []) {
    if(isset($config['loader_paths'])) {
      $this->paths = $config['loader_paths'];
    }
  }

  private function getTwig() {
    if(!$this->twig) {
      $config = $this->getConfig();
      $loader = new \Twig_Loader_Filesystem($this->paths);
      $this->twig = new \Twig_Environment($loader, [
        'cache' => $config->getCacheDir().DIRECTORY_SEPARATOR.'twig',
        'auto_reload' => TRUE,
      ]);
    }
    return $this->twig;
  }

  public function getParsers(): array {
    $config = $this->getConfig();
    return [new TwigParser($this->getTwig(), $config->getVariableFactory())];
  }

  public function getRenderers(): array {
    $config = $this->getConfig();
    return [new TwigRenderer($this->getTwig(), $config->getVariables(), $config->getStyles(), $config->getScripts())];
  }
}