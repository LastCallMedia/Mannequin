<?php


namespace LastCall\Patterns\Twig\Discovery;


use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Metadata\MetadataParserInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Variable\VariableFactoryInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use LastCall\Patterns\Core\Exception\InvalidVariableException;
use LastCall\Patterns\Twig\Pattern\TwigPattern;

class TwigFileDiscovery implements DiscoveryInterface {

  /**
   * @var \Twig_LoaderInterface|\Twig_SourceContextLoaderInterface|\Twig_ExistsLoaderInterface
   */
  private $loader;
  private $finder;
  private $variableFactory;
  private $prefix = 'twig://';
  private $metadataParser;

  public function __construct(\Twig_LoaderInterface $loader, Finder $finder, VariableFactoryInterface $variableFactory, MetadataParserInterface $metadataParser) {
    if(!$loader instanceof \Twig_SourceContextLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_SourceContextLoaderInterface');
    }
    if(!$loader instanceof \Twig_ExistsLoaderInterface) {
      throw new \InvalidArgumentException('Twig loader must implement Twig_ExistsLoaderInterface');
    }
    $this->loader = $loader;
    $this->finder = $finder;
    $this->variableFactory = $variableFactory;
    $this->metadataParser = $metadataParser;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->finder->files() as $fileInfo) {
      if($pattern = $this->parseFile($fileInfo)) {
        $patterns[] = $pattern;
      }
    }
    return new PatternCollection($patterns);
  }

  public function parseFile(SplFileInfo $fileInfo) {
    if($this->loader->exists($fileInfo->getRelativePathname())) {
      $id = $this->prefix . $fileInfo->getRelativePathname();
      $source = $this->loader->getSourceContext($fileInfo->getRelativePathname());

      $pattern = new TwigPattern($id, $source);

      if($this->metadataParser->hasMetadata($pattern)) {
        $metadata = $this->metadataParser->getMetadata($pattern);
        $pattern->setName($metadata['name']);
        $pattern->setTags($metadata['tags']);
        $pattern->setVariables($metadata['variables']);
      }
      return $pattern;
    }
  }

  private function createPatternFromTemplate(\Twig_TemplateWrapper $template) {
    $pathname = $template->getSourceContext()->getName();
    $id = $pathname;
    $name = $this->buildNameFromPathname($pathname);
    $tags = [];
    $variables = new VariableSet([]);
    if($template->hasBlock('patterninfo')) {
      $data = $this->parsePatternInfoBlock($template);
      if(isset($data['name'])) {
        $name = $data['name'];
      }
      if(isset($data['tags'])) {
        $tags = $data['tags'];
      }
      if(isset($data['variables'])) {
        $variables = $this->createVariableSet($data['variables']);
      }
    }
    $pattern = new TwigPattern($id, $name, $pathname, $variables);
    foreach($tags as $name => $value) {
      $pattern->addTag($name, $value);
    }
    $pattern->addTag('renderer', 'twig');
    return $pattern;
  }

  private function parsePatternInfoBlock(\Twig_TemplateWrapper $template) {
    $data = Yaml::parse($template->renderBlock('patterninfo'));
    if(!$data || !is_array($data)) {
      throw new \RuntimeException(sprintf('Unable to parse info block in %s', $template->getSourceContext()->getName()));
    }
    return $data;
  }

  private function buildNameFromPathname($pathname) {
    $basename = basename($pathname, '.'.pathinfo($pathname, PATHINFO_EXTENSION));
    return ucfirst(strtr($basename, [
      '-' => ' ',
      '_' => ' ',
    ]));
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
}