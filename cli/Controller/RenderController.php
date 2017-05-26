<?php

namespace LastCall\Mannequin\Cli\Controller;

use LastCall\Mannequin\Cli\Ui\UiRenderer;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

  public function indexAction() {
    return $this->getUiFile('index.html');
  }

  public function staticAction($name) {
    if(strpos($name, 'static/') === 0) {
      return $this->getUiFile($name);
    }
    // @todo: Assets need to be checked here.
    return new Response($name);
  }

  private function getUiFile($name) {
    $filename = sprintf(__DIR__.'/../../ui/build/%s', $name);
    if(file_exists($filename)) {
      return new BinaryFileResponse($filename);
    }
    throw new NotFoundHttpException('File not found.');
  }

  public function manifestAction() {
    $manifest = $this->renderer->renderManifest($this->collection, $this->generator);
    return new JsonResponse($manifest);
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