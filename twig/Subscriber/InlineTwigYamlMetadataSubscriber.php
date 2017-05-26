<?php


namespace LastCall\Mannequin\Twig\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
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

  public function __construct(VariableFactoryInterface $factory, \Twig_Environment $environment) {
    $this->parser = new YamlMetadataParser($factory);
    $this->twig = $environment;
  }
 
  public function getYamlMetadata(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    if($pattern instanceof TwigPattern) {
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
          foreach($metadata['tags'] as $k => $v) {
            $pattern->addTag($k, $v);
          }
          $pattern->getVariables()->merge($metadata['variables']);
        }
      }
    }
  }


}