<?php


namespace LastCall\Patterns\Cli\Controller;

use LastCall\Patterns\Core\Labeller;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class PatternController {

  private $collection;
  private $renderer;

  public function __construct(PatternCollection $collection, RendererInterface $renderer, Labeller $labeller, EngineInterface $templating, UrlGeneratorInterface $generator) {
    $this->collection = $collection;
    $this->renderer = $renderer;
    $this->labeler = $labeller;
    $this->templating = $templating;
    $this->generator = $generator;
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
      'page_title' => $this->labeler->getCollectionLabel($this->collection),
      'navigation' => $this->buildNavigation($this->collection),
      'patterns' => $this->buildCollectionPatterns($this->collection->getPatterns()),
      'patterns_nav' => $this->buildCollectionNav($this->collection->getPatterns()),
    ]);
    return new Response($output);
  }

  /**
   * Single pattern view action.
   */
  public function patternAction(PatternInterface $pattern) {
    $output = $this->templating->render('pattern', [
      'page_title' => 'Pattern: ' . $pattern->getName(),
      'navigation' => $this->buildNavigation($this->collection),
      'rendered_url' => $this->generator->generate('pattern_render', ['pattern' => $pattern->getId()]),
    ]);
    return new Response($output);
  }

  public function collectionAction(PatternCollection $collection) {
    $output = $this->templating->render('collection', [
      'page_title' => $this->labeler->getCollectionLabel($collection),
      'navigation' => $this->buildNavigation($this->collection),
      'patterns' => $this->buildCollectionPatterns($collection->getPatterns()),
      'patterns_nav' => $this->buildCollectionNav($collection->getPatterns()),
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

  private function buildNavigation(PatternCollection $rootCollection) {
    $groups = [
      $rootCollection->withTag('type', 'atom'),
      $rootCollection->withTag('type', 'molecule'),
      $rootCollection->withTag('type', 'element')
    ];
    $nav = [];
    foreach($groups as $group) {
      $grouping = [
        'url' => $this->generator->generate('collection_index', ['collection' => $group->getId()]),
        'title' => $this->labeler->getCollectionLabel($group),
        'below' => $this->buildPatternLinks($group->getPatterns())
      ];
      $nav[] = $grouping;
    }
    return $this->templating->render('new-navigation', [
      'tree' => $nav,
    ]);
  }

  private function buildPatternLinks(array $patterns) {
    $links = [];
    foreach($patterns as $pattern) {
      $links[] = [
        'url' => $this->generator->generate('pattern_view', ['pattern' => $pattern->getId()]),
        'title' => $this->labeler->getPatternLabel($pattern),
      ];
    }
    return $links;
  }

  private function buildCollectionPatterns(array $patterns) {
    $render = [];
    foreach($patterns as $pattern) {
      $render[] = $this->templating->render('pattern-teaser', [
        'id' => 'pattern-' . $pattern->getId(),
        'title' => $this->labeler->getPatternLabel($pattern),
        'rendered_url' => $this->generator->generate('pattern_render', ['pattern' => $pattern->getId()])
      ]);
    }
    return $render;
  }

  private function buildCollectionNav(array $patterns) {
    $render = [];
    foreach($patterns as $pattern) {
      $render[] = [
        'url' => '#' . 'pattern-' . $pattern->getId(),
        'title' => $this->labeler->getPatternLabel($pattern),
      ];
    }
    return $render;
  }


}