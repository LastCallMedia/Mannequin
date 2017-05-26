<?php


namespace LastCall\Mannequin\Core;


use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\Exception\InvalidVariableException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use LastCall\Mannequin\Core\Variable\VariableSet;

class YamlMetadataParser {

  public function parse($yaml, $exceptionIdentifier = 'unknown') {
    try {
      $yaml = Yaml::parse($yaml);
    }
    catch(ParseException $e) {
      throw new TemplateParsingException(sprintf('Unable to parse YAML metadata in %s', $exceptionIdentifier), $e->getCode(), $e);
    }
    return $this->processMetadata($yaml, $exceptionIdentifier);
  }

  private function processMetadata($metadata, $exceptionIdentifier) {
    if(!is_array($metadata)) {
      throw new TemplateParsingException(sprintf('Metadata must be an array in %s', $exceptionIdentifier));
    }
    $metadata += [
      'name' => '',
      'description' => '',
      'tags' => [],
      'definition' => [],
      'sets' => [],
      'variables' => [],
    ];
    if(!is_string($metadata['name'])) {
      throw new TemplateParsingException(sprintf('Name must be a string in %s', $exceptionIdentifier));
    }
    if(!is_string($metadata['description'])) {
      throw new TemplateParsingException(sprintf('Description must be a string in %s', $exceptionIdentifier));
    }
    if(!is_array($metadata['tags'])) {
      throw new TemplateParsingException(sprintf('Tags must be an associative array in %s', $exceptionIdentifier));
    }
    if(!is_array($metadata['variables'])) {
      throw new TemplateParsingException(sprintf('Variables must be an associative array in %s', $exceptionIdentifier));
    }
    if(!is_array($metadata['definition'])) {
      throw new TemplateParsingException(sprintf('Definition must be an associative array in %s', $exceptionIdentifier));
    }
    if(!is_array($metadata['sets'])) {
      throw new TemplateParsingException(sprintf('Sets must be an associative array in %s', $exceptionIdentifier));
    }
    $metadata['definition'] = $this->createDefinition($metadata, $exceptionIdentifier);
    $metadata['sets'] = $this->createSets($metadata, $exceptionIdentifier);
    return $metadata;
  }

  public function createSets(array $metadata, $exceptionIdentifier) {
    $defaultSet = [];

    foreach($metadata['variables'] as $name => $variable) {
      if(isset($variable['value'])) {
        $defaultSet[$name] = $variable['value'];
      }
    }

    return [
      'default' => new Set('Default', $defaultSet)
    ];
  }

  public function createDefinition(array $metadata, $exceptionIdentifier) {
    $definition = [];
    foreach($metadata['variables'] as $name => $variable) {
      $definition[$name] = $variable['type'];
    }
    return new Definition($definition);
  }
}