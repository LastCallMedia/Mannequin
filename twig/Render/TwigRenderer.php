<?php


namespace LastCall\Mannequin\Twig\Render;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\Rendered;
use LastCall\Mannequin\Core\Render\RenderedInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Variable\VariableInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigRenderer implements RendererInterface {

  private $twig;
  private $globals;
  private $styles = [];
  private $scripts = [];

  public function __construct(\Twig_Environment $twig, VariableSet $globals = NULL, array $styles = [], array $scripts = []) {
    $this->twig = $twig;
    $this->globals = $globals;
    $this->styles = $styles;
    $this->scripts = $scripts;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof TwigPattern;
  }

  public function render(PatternInterface $pattern, VariableSet $overrides = NULL): RenderedInterface {
    $rendered = new Rendered($pattern, $this->styles, $this->scripts);
    $variables = $this->prepareVariables($pattern, $overrides, $rendered);
    $rendered->setMarkup($this->twig->render($pattern->getSource()->getName(), $variables));
    return $rendered;
  }

  private function prepareVariables(PatternInterface $pattern, VariableSet $overrides = NULL, RenderedInterface $rendered) {
    $variables = $pattern->getVariables();
    if($this->globals) {
      $variables = $variables->applyGlobals($this->globals);
    }
    if($overrides) {
      $variables = $variables->applyOverrides($overrides);
    }
    $manifested = $variables->manifest();

    foreach($manifested as &$var) {
      if($var instanceof RenderedInterface) {
        // @todo: Ideally, scripts and styles wouldn't be added until this is
        // used in the template.
        $rendered->addScripts($var->getScripts());
        $rendered->addStyles($var->getStyles());
        $var = new \Twig_Markup($var->getMarkup(), $this->twig->getCharset());
      }
    }
    return $manifested;
  }
}