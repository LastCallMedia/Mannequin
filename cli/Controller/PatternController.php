<?php


namespace LastCall\Patterns\Cli\Controller;

use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Render\Renderer;
use LastCall\Patterns\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PatternController {

  private $collection;
  private $renderer;

  public function __construct(PatternCollection $collection, Renderer $renderer, UiInterface $ui, UrlGeneratorInterface $generator) {
    $this->collection = $collection;
    $this->renderer = $renderer;
    $this->ui = $ui;
    $this->generator = $generator;
  }

  public function convertPattern($id) {
    if($pattern = $this->collection->getPattern($id)) {
      return $pattern;
    }
    throw new NotFoundHttpException(sprintf('Unknown pattern: %s', $id));
  }

  public function convertTag($spec) {
    list($type, $value) = explode(':', $spec);
    $patterns = array_filter($this->collection->getPatterns(), function(PatternInterface $pattern) use ($type, $value) {
      return $pattern->hasTag($type, $value);
    });
    return new PatternCollection($patterns, sprintf('tag:%s:%s', $type, $value),
      sprintf('%s - %s', $type, $value)
    );
  }

  public function indexAction() {
    $output = $this->ui->decorateIndex($this->collection, $this->generator);
    return new Response($output);
  }

  public function patternAction(PatternInterface $pattern) {
    $rendered = $this->renderer->render($pattern);
    $output = $this->ui->decorateRendered($rendered);
    return new Response($output);
  }

  public function tagAction(PatternCollection $tag) {
    $output = $this->ui->decorateIndex($tag, $this->generator);
    return new Response($output);
  }
}