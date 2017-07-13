<?php

namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\HtmlDecorator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RenderController {

  private $collection;
  private $engine;

  public function __construct(PatternCollection $collection, EngineInterface $engine, HtmlDecorator $decorator) {
    $this->collection = $collection;
    $this->engine = $engine;
    $this->decorator = $decorator;
  }

  private function renderPattern($pattern, $set) {
    if($pattern = $this->collection->get($pattern)) {
      if($set = $pattern->getVariableSets()[$set]) {
        return $this->engine->render($pattern, $set);
      }
    }
    throw new NotFoundHttpException('Pattern not found.');
  }

  public function renderAction($pattern, $set) {
    $rendered = $this->renderPattern($pattern, $set);
    $decorated = $this->decorator->decorate($rendered->getMarkup(), $rendered->getScripts(), $rendered->getStyles());
    return new Response($decorated);
  }

  public function renderRawAction($pattern, $set) {
    $rendered = $this->renderPattern($pattern, $set);
    return new Response($rendered->getMarkup());
  }

  public function renderSourceAction($pattern) {
    if($pattern = $this->collection->get($pattern)) {
      $markup = $this->engine->renderSource($pattern);
      return new Response($markup);
    }
  }
}