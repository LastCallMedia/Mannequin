<?php


namespace LastCall\Mannequin\Core\Discovery;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChainDiscovery implements DiscoveryInterface {

  private $discoverers = [];
  private $dispatcher;

  public function __construct(array $discoverers = [], EventDispatcherInterface $dispatcher) {
    foreach($discoverers as $discoverer) {
      if(!$discoverer instanceof DiscoveryInterface) {
        throw new \InvalidArgumentException(sprintf('Discoverer must implement %s', DiscoveryInterface::class));
      }
      $this->discoverers[] = $discoverer;
    }
    $this->dispatcher = $dispatcher;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->discoverers as $discoverer) {
      foreach($discoverer->discover() as $pattern) {
        $patterns[] = $pattern;
      }
    }
    $collection = new PatternCollection($patterns);
    foreach($collection as $pattern) {
      $this->dispatcher->dispatch(PatternEvents::DISCOVER, new PatternDiscoveryEvent($pattern, $collection));
    }
    return $collection;
  }
}