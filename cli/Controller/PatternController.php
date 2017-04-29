<?php


namespace LastCall\Patterns\Cli\Controller;

use LastCall\Patterns\Core\Pattern\Pattern;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class PatternController {

  private $collection;
  private $renderer;

  public function __construct(PatternCollection $collection, Renderer $renderer, EngineInterface $templating, UrlGeneratorInterface $generator) {
    $this->collection = $collection;
    $this->renderer = $renderer;
    $this->generator = $generator;
    $this->templating = $templating;
  }

  public function convertCollection($spec) {
    $parts = explode('|', $spec);
    $collection = $this->collection;
    while($part = array_shift($parts)) {
      list($type, $value) = explode(':', $spec);
      $collection = $collection->withTag($type, $value);
    }
    return $collection;
  }

  public function convertPattern($id, Request $request) {
    /** @var PatternCollection $collection */
    if($request->attributes->has('collection')) {
      $collection = $request->attributes->get('collection');
    }
    else {
      $collection = $this->collection;
    }
    if($pattern = $collection->get($id)) {
      return $pattern;
    }
    throw new NotFoundHttpException(sprintf('Unknown pattern: %s', $id));
  }

  public function rootAction() {
    $output = $this->templating->render('collection', [
      'collection' => $this->collection,
      'generator' => $this->generator,
    ]);
    return new Response($output);
  }

  /**
   * Single pattern view action.
   */
  public function patternAction(PatternInterface $pattern) {
    $output = $this->templating->render('pattern', [
      'pattern' => $pattern,
      'generator' => $this->generator
    ]);
    return new Response($output);
  }

  public function collectionAction(PatternCollection $collection) {
    $output = $this->templating->render('collection', [
      'collection' => $collection,
      'generator' => $this->generator,
    ]);
    return new Response($output);
  }

  public function renderAction(PatternInterface $pattern) {
    $rendered = $this->renderer->render($pattern);
    $output = $this->templating->render('pattern-render', [
      'rendered' => $rendered,
    ]);
    return new Response($output);
  }
}