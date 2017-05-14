<?php


namespace LastCall\Patterns\Twig\Discovery;


use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Exception\TemplateParsingException;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Variable\VariableFactoryInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use LastCall\Patterns\Core\Exception\InvalidVariableException;
use LastCall\Patterns\Twig\Pattern\TwigPattern;

class TwigFileDiscovery implements DiscoveryInterface {

  private $twig;
  private $finder;
  private $variableFactory;

  public function __construct(\Twig_Environment $twig, Finder $finder, VariableFactoryInterface $variableFactory) {
    $this->twig = $twig;
    $this->finder = $finder;
    $this->variableFactory = $variableFactory;
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
    if($this->twig->getLoader()->exists($fileInfo->getRelativePathname())) {
      try {
        $template = $this->twig->load($fileInfo->getRelativePathname());
        return $this->createPatternFromTemplate($template);
      }
      catch(\Throwable $err) {
        throw new TemplateParsingException(sprintf('Unable to parse template: %s', $err->getMessage()), $err->getCode(), $err);
      }
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