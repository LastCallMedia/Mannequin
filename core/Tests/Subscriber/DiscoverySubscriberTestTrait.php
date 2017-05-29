<?php


namespace LastCall\Mannequin\Core\Tests\Subscriber;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;

trait DiscoverySubscriberTestTrait {

  protected function dispatchDiscover(EventSubscriberInterface $subscriber, PatternInterface $pattern): PatternDiscoveryEvent {
    $dispatcher = new EventDispatcher();
    $dispatcher->addSubscriber($subscriber);
    return $dispatcher->dispatch(PatternEvents::DISCOVER, new PatternDiscoveryEvent($pattern));
  }

}