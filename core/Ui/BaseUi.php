<?php


namespace LastCall\Patterns\Core\Ui;


use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\RenderedInterface;
use Symfony\Component\Templating\EngineInterface;

class BaseUi implements UiInterface {

  public function __construct(EngineInterface $engine) {
    $this->templating = $engine;
  }

  public function decorateIndex(PatternCollection $collection): string {
    $list = [];
    foreach($collection->getPatterns() as $pattern) {
      $list[] = $pattern->getName();
    }
    return $this->templating->render('index.tpl.php', [
      'title' => $collection->getName(),
      'links' => $list,
    ]);
  }

  public function decorateRendered(RenderedInterface $rendered): string {
    return $this->templating->render('pattern.tpl.php', [
      'title' => $rendered->getName(),
      'markup' => $rendered->getMarkup(),
    ]);
  }
}