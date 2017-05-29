<?php


namespace LastCall\Mannequin\Twig\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InlineTwigYamlMetadataSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      PatternEvents::DISCOVER => 'getYamlMetadata'
    ];
  }

  private $parser;
  private $twig;

  public function __construct(\Twig_Environment $environment, YamlMetadataParser $parser = NULL) {
    $this->parser = $parser ?: new YamlMetadataParser();
    $this->twig = $environment;
  }
 
  public function getYamlMetadata(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    if($pattern instanceof TwigPattern) {
      // Exit early if there's absolutely no patterninfo block.
      if(strpos($pattern->getSource()->getCode(), 'patterninfo') === FALSE) {
        return;
      }
      if($this->twig->getLoader()->exists($pattern->getSource()->getName())) {
        $template = $this->twig->load($pattern->getSource()->getName());
        if($template->hasBlock('patterninfo')) {
          $yaml = $template->renderBlock('patterninfo');
          $metadata = $this->parser->parse($yaml);

          if(empty($pattern->getName()) && $metadata['name']) {
            $pattern->setName($metadata['name']);
          }
          if(empty($pattern->getDescription()) && $metadata['description']) {
            $pattern->setDescription($metadata['description']);
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

}