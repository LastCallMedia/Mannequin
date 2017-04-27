<?php


namespace LastCall\Patterns\Core\Ui;


use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\RenderedInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class BaseUi implements UiInterface {

  public function __construct(EngineInterface $engine) {
    $this->templating = $engine;
  }

  public function decorateIndex(PatternCollection $collection, UrlGeneratorInterface $generator): string {
    return $this->templating->render('index.tpl.php', [
      'title' => $collection->getName(),
      'patterns' => $collection->getPatterns(),
      'generator' => $generator,
    ]);
  }

  public function decorateRendered(RenderedInterface $rendered): string {
    return $this->templating->render('pattern.tpl.php', [
      'title' => $rendered->getName(),
      'markup' => $rendered->getMarkup(),
    ]);
  }
}