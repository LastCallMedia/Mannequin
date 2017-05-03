<?php

namespace LastCall\Patterns\Core\Render;

use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Pattern\TemplatePattern;
use Pimple\Container;
use Symfony\Component\Templating\EngineInterface;

class TemplatingRenderer implements RendererInterface {

  private $engine;
  private $styles;
  private $scripts;

  public function __construct(EngineInterface $engine, array $styles = [], array $scripts = []) {
    $this->engine = $engine;
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof TemplatePattern;
  }

  public function render(PatternInterface $pattern): RenderedInterface {
    $rendered = new Rendered($pattern, $this->styles, $this->scripts);
    $vars = $this->extractVars($pattern);
    $markup = $this->engine->render($pattern->getTemplateReference(), $vars);
    $rendered->setMarkup($markup);
    return $rendered;
  }

  private function extractVars(PatternInterface $pattern) {
    /** @var \Pimple\Container $vars */
    $vars = $pattern->getTemplateVariables();

    if($vars && $vars instanceof Container) {
      $_vars = [];
      foreach($vars->keys() as $k) {
        $_vars[$k] = $vars[$k];
      }
      return $_vars;
    }

    return $vars;
  }
}