<?php


namespace LastCall\Mannequin\Twig;


use Psr\Cache\CacheItemPoolInterface;

class TwigInspectorCacheDecorator implements TwigInspectorInterface {

  public function __construct(TwigInspectorInterface $decorated, CacheItemPoolInterface $cache) {
    $this->decorated = $decorated;
    $this->cache = $cache;
  }

  public function inspectLinked(\Twig_Source $source): array {
    $item = $this->cache->getItem($this->getCid($source));
    if($item->isHit()) {
      return $item->get();
    }
    $item->set($this->decorated->inspectLinked($source));
    $this->cache->save($item);
    return $item->get();
  }

  private function getCid(\Twig_Source $source) {
    return md5($source->getCode());
  }

}