<?php


namespace LastCall\Mannequin\Core\Extension;


use LastCall\Mannequin\Core\Discovery\TemplateDiscovery;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\Variable\PatternResolver;
use LastCall\Mannequin\Core\Variable\ScalarResolver;
use LastCall\Mannequin\Core\Variable\VariableSet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CoreExtension extends AbstractExtension {

  public function getVariableResolvers(): array {
    return [
      new ScalarResolver(),
      new PatternResolver(function() {}),
    ];
  }

  public function attachToDispatcher(EventDispatcherInterface $dispatcher) {
    $dispatcher->addSubscriber(new YamlFileMetadataSubscriber());
    $dispatcher->addSubscriber(new LastChanceNameSubscriber());
  }
}