<?php


namespace LastCall\Mannequin\Twig\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigIncludeSubscriber implements EventSubscriberInterface {

  private $inspector;
  private $prefix;

  public static function getSubscribedEvents() {
    return [
      PatternEvents::DISCOVER => 'detect',
    ];
  }

  public function __construct(TwigInspectorInterface $inspector, string $prefix = 'twig') {
    $this->inspector = $inspector;
    $this->prefix = $prefix;
  }

  public function detect(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    $collection = $event->getCollection();

    if($pattern instanceof TwigPattern) {
      $included = $this->inspector->inspectLinked($pattern->getSource());
      foreach ($included as $name) {
        if($collection->has($name)) {
          $pattern->addUsedPattern($collection->get($name));
        }
      }
    }
  }
}