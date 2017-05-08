<?php


namespace LastCall\Patterns\Twig\Render;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Render\Rendered;
use LastCall\Patterns\Core\Render\RenderedInterface;
use LastCall\Patterns\Core\Render\RendererInterface;
use LastCall\Patterns\Core\Variable\VariableInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use LastCall\Patterns\Twig\Pattern\TwigPattern;

class TwigRenderer implements RendererInterface {

  private $twig;
  private $globals;
  private $styles = [];
  private $scripts = [];

  public function __construct(\Twig_Environment $twig, VariableSet $globals, array $styles = [], array $scripts = []) {
    $this->twig = $twig;
    $this->globals = $globals;
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof TwigPattern;
  }

  public function render(PatternInterface $pattern): RenderedInterface {
    $variables = $pattern->getVariables()->applyGlobals($this->globals);
    $rendered = new Rendered($pattern, $this->styles, $this->scripts);
    $rendered->setMarkup($this->twig->render($pattern->getFilename(), $variables->manifest()));
    return $rendered;
  }

  private function getVariables($pattern) {
    $variables = [];

    foreach($pattern->getVariables() as $key => $value) {
      if($value instanceof VariableInterface) {
        $variables[$key] = $value->getValue();
      }
      else {
        $variables[$key] = $value;
      }
    }
    return $variables;
  }
}