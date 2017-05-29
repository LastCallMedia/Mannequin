<?php


namespace LastCall\Mannequin\Twig\Engine;


use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\Rendered;
use LastCall\Mannequin\Core\Render\RenderedInterface;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\SetResolver;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigEngine implements \LastCall\Mannequin\Core\Engine\EngineInterface {

  private $twig;
  private $styles = [];
  private $scripts = [];

  public function __construct(\Twig_Environment $twig, SetResolver $setResolver, array $styles = [], array $scripts = []) {
    $this->twig = $twig;
    $this->styles = $styles;
    $this->scripts = $scripts;
    $this->setResolver = $setResolver;
  }

  public function supports(PatternInterface $pattern): bool {
    return $pattern instanceof TwigPattern;
  }

  public function render(PatternInterface $pattern, Set $set): RenderedInterface {
    if($this->supports($pattern)) {
      $rendered = new Rendered($pattern);
      $variables = $this->setResolver->resolveSet($pattern->getVariableDefinition(), $set);
      $rendered->setMarkup($this->twig->render($pattern->getSource()->getName(), $variables));
      $rendered->setStyles($this->styles);
      $rendered->setScripts($this->scripts);
      return $rendered;
    }
    throw new UnsupportedPatternException('Unsupported pattern.');
  }

  public function renderSource(PatternInterface $pattern): string {
    if($this->supports($pattern)) {
      return $pattern->getSource()->getCode();
    }
    throw new UnsupportedPatternException('Unsupported pattern.');
  }
}