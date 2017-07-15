<?php


namespace LastCall\Mannequin\Twig\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\TwigInspector;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InlineTwigYamlMetadataSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      PatternEvents::DISCOVER => 'getYamlMetadata'
    ];
  }

  private $parser;
  private $inspector;

  public function __construct(TwigInspectorInterface $inspector, YamlMetadataParser $parser = NULL) {
    $this->inspector = $inspector;
    $this->parser = $parser ?: new YamlMetadataParser();
  }
 
  public function getYamlMetadata(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    if($pattern instanceof TwigPattern) {
      $yaml = $this->inspector->inspectPatternData($pattern->getSource());
      if($yaml !== NULL) {
        $metadata = $this->parser->parse($yaml);

        if(empty($pattern->getName()) && $metadata['name']) {
          $pattern->setName($metadata['name']);
        }
        if(empty($pattern->getDescription()) && $metadata['description']) {
          $pattern->setDescription($metadata['description']);
        }
        if(!empty($metadata['group'])) {
          $pattern->setGroup($metadata['group']);
        }
        $pattern->setVariableDefinition($metadata['definition']);
        foreach($metadata['tags'] as $k => $v) {
          $pattern->addTag($k, $v);
        }
        foreach($metadata['sets'] as $k => $set) {
          $pattern->addVariableSet($k, $set);
        }
      }
    }
  }

}