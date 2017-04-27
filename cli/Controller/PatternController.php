<?php


namespace LastCall\Patterns\Cli\Controller;

use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Render\Renderer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PatternController {

  private $collection;
  private $renderer;

  public function __construct(PatternCollection $collection, Renderer $renderer) {
    $this->collection = $collection;
    $this->renderer = $renderer;
  }

  public function indexAction() {
    return $this->collection;
  }

  public function patternAction($id) {
    if($pattern = $this->collection->getPattern($id)) {
      return $pattern;
    }
    throw new NotFoundHttpException(sprintf('%s is not a valid pattern.', $id));
  }
}