<?php


namespace LastCall\Mannequin\Core;


use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\Exception\InvalidVariableException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use LastCall\Mannequin\Core\Variable\VariableSet;

class YamlMetadataParser {

  public function __construct(VariableFactoryInterface $factory) {
    $this->variableFactory = $factory;
  }

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
    $metadata['variables'] = $this->createVariableSet($metadata['variables'], $exceptionIdentifier);
    return $metadata;
  }

  private function createVariableSet(array $variables, $exceptionIdentifier) {
    $setVars = [];
    foreach($variables as $key => $info) {
      if(!is_array($info) || empty($info['type'])) {
        throw new InvalidVariableException(sprintf('%s must be an array specifying the type in %s', $key, $exceptionIdentifier));
      }
      $info+= ['value' => NULL];
      if($info['type'] === 'pattern' && is_array($info['value'])) {
        $info['value']['variables'] = $this->createVariableSet($info['value']['variables'], $exceptionIdentifier);
      }
      $setVars[$key] = $this->variableFactory->create($info['type'], $info['value']);
    }
    return new VariableSet($setVars);
  }
}