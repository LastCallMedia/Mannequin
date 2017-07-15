<?php


namespace LastCall\Mannequin\Core\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LastChanceNameSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      PatternEvents::DISCOVER => ['setPatternName', -100],
    ];
  }

  public function setPatternName(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    if(empty($pattern->getName())) {
      if($pattern instanceof TemplateFilePatternInterface) {
        $file = $pattern->getFile();
        $name = explode('.', $file->getBasename())[0];
        $name = ucfirst(strtr(trim($name, '-_.'), [
          '-' => ' ',
          '_' => ' ',
        ]));
        $pattern->setName($name);
      }
      else {
        $pattern->setName($pattern->getId());
      }

    }
  }
}