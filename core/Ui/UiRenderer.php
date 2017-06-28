<?php


namespace LastCall\Mannequin\Core\Ui;


use LastCall\Mannequin\Core\Labeller;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface as PatternEngineInterface;
use LastCall\Mannequin\Core\Variable\Set;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface as TemplatingEngineInterface;

class UiRenderer {

  private $renderer;
  private $templating;
  private $labeller;

  public function __construct(PatternEngineInterface $renderer, TemplatingEngineInterface $engine, Labeller $labeller) {
    $this->renderer = $renderer;
    $this->templating = $engine;
    $this->labeller = $labeller;
  }

  public function renderManifest(PatternCollection $collection, UrlGeneratorInterface $generator) {
    $manifest = [];
    $tags = [];
    foreach($collection as $pattern) {
      $id = $pattern->getId();
      $manifest['patterns'][] = [
        'id' => $id,
        'source' => $generator->generate('pattern_render_source_raw', ['pattern' => $id]),
        'name' => $pattern->getName(),
        'description' => $pattern->getDescription(),
        'tags' => $pattern->getTags(),
        'sets' => $this->renderPatternSets($pattern, $generator),
        'used' => $this->renderPatternUsed($pattern),
        'aliases' => $pattern->getAliases(),
      ];
      foreach($pattern->getTags() as $k => $v) {
        if(!isset($tags[$k])) {
          $tags[$k] = [
            'unknown' => $this->labeller->getTagLabel($k, 'unknown')
          ];
        }
        if(!isset($tags[$k][$v])) {
          $tags[$k][$v] = $this->labeller->getTagLabel($k, $v);
        }
      }
    }
    foreach($tags as $k => &$kTags) {
      uksort($kTags, function($a, $b) use ($k) {
        return $this->labeller->getTagWeight($k, $a) - $this->labeller->getTagWeight($k, $b);
      });
    }
    $manifest['tags'] = $tags;

    return $manifest;
  }

  private function renderPatternSets(PatternInterface $pattern, UrlGeneratorInterface $generator) {
    $sets = [];
    foreach($pattern->getVariableSets() as $id => $set) {
      $sets[] = [
        'id' => $id,
        'name' => $set->getName(),
        'description' => $set->getDescription(),
        'source' => $generator->generate('pattern_render_raw', ['pattern' => $pattern->getId(), 'set' => $id], UrlGeneratorInterface::RELATIVE_PATH),
        'rendered' => $generator->generate('pattern_render', ['pattern' => $pattern->getId(), 'set' => $id], UrlGeneratorInterface::RELATIVE_PATH),
      ];
    }
    return $sets;
  }

  private function renderPatternUsed(PatternInterface $pattern) {
    return array_map(function(PatternInterface $usedPattern) {
      return $usedPattern->getId();
    }, $pattern->getUsedPatterns());
  }

  public function renderPattern(PatternInterface $pattern, Set $set) {
    $rendered = $this->renderer->render($pattern, $set);
    return $this->templating->render('rendered.html.php', [
      'title' => $pattern->getName(),
      'markup' => $rendered->getMarkup(),
      'styles' => $rendered->getStyles(),
      'scripts' => $rendered->getScripts(),
    ]);
  }

  public function renderPatternRaw(PatternInterface $pattern, Set $set) {
    return $this->renderer->render($pattern, $set);
  }

  public function renderSource(PatternInterface $pattern) {
    $source = $this->renderer->renderSource($pattern);
    return $this->templating->render('source.html.php', [
      'title' => sprintf('Source for %s', $pattern->getName()),
      'source' => $source,
    ]);
  }

  public function renderSourceRaw(PatternInterface $pattern) {
    return $this->renderer->renderSource($pattern);
  }
}