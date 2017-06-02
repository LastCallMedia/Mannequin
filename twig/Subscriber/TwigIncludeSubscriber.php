<?php


namespace LastCall\Mannequin\Twig\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigIncludeSubscriber implements EventSubscriberInterface {

  private $twig;
  private $cache;

  private $prefix = 'drupal';

  public static function getSubscribedEvents() {
    return [
      PatternEvents::DISCOVER => 'detectIncludedPatterns',
    ];
  }

  public function __construct(\Twig_Environment $twig, CacheItemPoolInterface $cache) {
    $this->twig = $twig;
    $this->cache = $cache;
  }

  public function detectIncludedPatterns(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    $collection = $event->getCollection();

    if($pattern instanceof TwigPattern) {
      $cacheItem = $this->cache->getItem(md5($pattern->getSource()->getCode()));
      if($cacheItem->isHit()) {
        $usedArr = $cacheItem->get();
      }
      else {
        $usedArr =  $this->detectUsedTemplates($pattern);
        $cacheItem->set($usedArr);
        $this->cache->save($cacheItem);
      }

      foreach($usedArr as $used) {
        $usedId = sprintf('%s://%s', $this->prefix, $used);
        if($collection->has($usedId)) {
          $pattern->addUsedPattern($collection->get($usedId));
        }
      }
    }
  }

  private function detectUsedTemplates(TwigPattern $pattern) {
    $parsed = $this->twig->parse($this->twig->tokenize($pattern->getSource()));
    $includes = self::walkNodes($parsed, \Twig_Node_Include::class);
    $embeds = self::walkNodes($parsed, \Twig_Node_Embed::class);
    $parents = self::getParents($parsed);

    return array_merge($includes, $embeds, $parents);
  }

  private static function getParents(\Twig_Node $node) {
    $includes = [];
    if($node->hasNode('parent')) {
      $parentNode = $node->getNode('parent');
      if($parentNode instanceof \Twig_Node_Expression_Constant) {
        $includes[] = $parentNode->getAttribute('value');
      }
    }
    return $includes;
  }

  private static function walkNodes(\Twig_Node $node, $forClass) {
    $includes = [];
    foreach($node as $child) {
      if($child instanceof \Twig_Node) {
        $includes = array_merge($includes, self::walkNodes($child, $forClass));
      }
    }
    if($node instanceof $forClass) {
      $expr = $node->getNode('expr');
      if($expr instanceof \Twig_Node_Expression_Constant) {
        $includes[] = $expr->getAttribute('value');
      }
    }
    return $includes;
  }
}