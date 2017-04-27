<?php


namespace LastCall\Patterns\Cli\Controller;

use LastCall\Patterns\Core\Pattern\PatternCollection;
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

  public function indexAction() {
    $output = $this->ui->decorateIndex($this->collection, $this->generator);
    return new Response($output);
  }

  public function patternAction($id) {
    if($pattern = $this->collection->getPattern($id)) {
      $rendered = $this->renderer->render($pattern);
      $output = $this->ui->decorateRendered($rendered);
      return new Response($output);
    }
    throw new NotFoundHttpException(sprintf('%s is not a valid pattern.', $id));
  }
}