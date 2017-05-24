<?php


namespace LastCall\Mannequin\Core\Metadata;


use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;
use Symfony\Component\Yaml\Yaml;

class YamlFileMetadataFactory implements MetadataFactoryInterface {

  protected $variableFactory;

  public function __construct(VariableFactoryInterface $variableFactory) {
    $this->variableFactory = $variableFactory;
  }

  public function hasMetadata(PatternInterface $pattern): bool {
    if($pattern instanceof TemplateFilePatternInterface) {
      $yamlFile = $this->getYamlFileForPatternFile($pattern->getFile());
      return file_exists($yamlFile);
    }
    return FALSE;
  }

  public function getMetadata(PatternInterface $pattern): array {
    if($pattern instanceof TemplateFilePatternInterface) {
      $yamlFile = $this->getYamlFileForPatternFile($pattern->getFile());
      if(!file_exists($yamlFile)) {
        throw new \InvalidArgumentException(sprintf('Metadata file %s does not exist', $yamlFile));
      }
      try {
        $yaml = Yaml::parse(file_get_contents($yamlFile));
        return $this->processMetadata($yaml);
      }
      catch(\Throwable $e) {
        throw new TemplateParsingException(sprintf('Unable to parse metadata file %s', $yamlFile), $e->getCode(), $e);
      }
    }
    throw new \InvalidArgumentException(sprintf('Pattern %s does not implement TemplateFilePatternInterface', $pattern->getId()));
  }

  protected function processMetadata($metadata) {
    if(!is_array($metadata)) {
      throw new TemplateParsingException('Metadata must be an array');
    }
    $metadata += [
      'name' => '',
      'description' => '',
      'tags' => [],
      'variables' => [],
    ];
    if(!is_string($metadata['name'])) {
      throw new TemplateParsingException('Name must be a string.');
    }
    if(!is_string($metadata['description'])) {
      throw new TemplateParsingException('Description must be a string');
    }
    if(!is_array($metadata['tags'])) {
      throw new TemplateParsingException('Tags must be an associative array');
    }
    if(!is_array($metadata['variables'])) {
      throw new TemplateParsingException('Variables must be an associative array');
    }
    $metadata['variables'] = $this->createVariableSet($metadata['variables']);
    return $metadata;
  }

  private function createVariableSet(array $variables) {
  $setVars = [];
  foreach($variables as $key => $info) {
    if(!is_array($info) || empty($info['type'])) {
      throw new InvalidVariableException(sprintf('%s must be an array specifying the type', $key));
    }
    $info+= ['value' => NULL];
    if($info['type'] === 'pattern' && is_array($info['value'])) {
      $info['value']['variables'] = $this->createVariableSet($info['value']['variables']);
    }
    $setVars[$key] = $this->variableFactory->create($info['type'], $info['value']);
  }
  return new VariableSet($setVars);
}

  private function getYamlFileForPatternFile(\SplFileInfo $patternFile) {
    $path = $patternFile->getPath();
    $basename = $patternFile->getBasename('.' .$patternFile->getExtension()) . '.yml';
    return sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $basename);
  }
}