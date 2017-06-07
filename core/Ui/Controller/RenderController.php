<?php

namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\UiRenderer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RenderController {

  private $collection;
  private $renderer;
  private $generator;

  public function __construct(PatternCollection $collection, UiRenderer $renderer, UrlGeneratorInterface $generator) {
    $this->collection = $collection;
    $this->renderer = $renderer;
    $this->generator = $generator;
  }

  public function manifestAction() {
    $manifest = $this->renderer->renderManifest($this->collection, $this->generator);
    $res = new JsonResponse($manifest);
    // @todo: Remove pretty print before release.
    $res->setEncodingOptions(JSON_PRETTY_PRINT);
    return $res;
  }

  public function renderAction($pattern) {
    if($pattern = $this->collection->get($pattern)) {
      $set = $pattern->getVariableSets()['default'];
      $rendered = $this->renderer->renderPattern($pattern, $set);
      return new Response($rendered);
    }
  }

  public function sourceAction($pattern) {
    if($pattern = $this->collection->get($pattern)) {
      $rendered = $this->renderer->renderSource($pattern);
      return new Response($rendered);
    }
  }
}