<?php


namespace LastCall\Patterns\Twig\Parser;


use LastCall\Patterns\Core\Exception\TemplateParsingException;
use LastCall\Patterns\Core\Parser\TemplateFileParserInterface;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class TwigParser implements TemplateFileParserInterface {

  private $twig;

  private $variableFactory;

  public function __construct(\Twig_Environment $twig, VariableFactory $factory) {
    $this->twig = $twig;
    $this->variableFactory = $factory;
  }

  public function supports(SplFileInfo $fileInfo): bool {
    return $fileInfo->getExtension() === 'twig' && $this->twig->getLoader()->exists($fileInfo->getRelativePathname());
  }

  public function parse(SplFileInfo $fileInfo): PatternInterface {
    try {
      $template = $this->twig->load($fileInfo->getRelativePathname());
      return $this->createPatternFromTemplate($template);
    }
    catch(\Throwable $err) {
      throw new TemplateParsingException(sprintf('Unable to parse template: %s', $err->getMessage()), $err->getCode(), $err);
    }

  }

  private function createPatternFromTemplate(\Twig_TemplateWrapper $template) {
    $pathname = $template->getSourceContext()->getName();
    $id = $pathname;
    $name = $this->buildNameFromPathname($pathname);
    $tags = [];
    $variables = $this->variableFactory->createSet([]);
    if($template->hasBlock('patterninfo')) {
      $data = $this->parsePatternInfoBlock($template);
      if(isset($data['name'])) {
        $name = $data['name'];
      }
      if(isset($data['tags'])) {
        $tags = $data['tags'];
      }
      if(isset($data['variables'])) {
        $variables = $this->variableFactory->createSet($data['variables']);
      }
    }
    $pattern = new TwigPattern($id, $name, $pathname, $variables);
    foreach($tags as $name => $value) {
      $pattern->addTag($name, $value);
    }
    return $pattern;
  }

  private function parsePatternInfoBlock(\Twig_TemplateWrapper $template) {
    $data = Yaml::parse($template->renderBlock('patterninfo'));
    if(!$data || !is_array($data)) {
      throw new \RuntimeException(sprintf('Unable to parse info block in %s', $template->getSourceContext()->getName()));
    }
    if($data && is_array($data)) {
      return $data;
    }

  }

  private function buildNameFromPathname($pathname) {
    $basename = basename($pathname, '.'.pathinfo($pathname, PATHINFO_EXTENSION));
    return ucfirst(strtr($basename, [
      '-' => ' ',
      '_' => ' ',
    ]));
  }
}