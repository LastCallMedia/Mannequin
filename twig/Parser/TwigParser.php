<?php


namespace LastCall\Patterns\Twig\Parser;


use LastCall\Patterns\Core\Parser\TemplateFileParserInterface;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class TwigParser implements TemplateFileParserInterface {

  private $twig;

  public function __construct(\Twig_Environment $twig) {
    $this->twig = $twig;
  }

  public function supports(\SplFileInfo $fileInfo): bool {
    return $fileInfo->getExtension() === 'twig';
  }

  public function parse(\SplFileInfo $fileInfo): PatternInterface {
    $template = $this->twig->load($fileInfo->getPathname());
    return $this->createPatternFromTemplateData($template, $fileInfo);
  }

  private function createPatternFromTemplateData(\Twig_TemplateWrapper $template, SplFileInfo $fileInfo) {
    $basename = $fileInfo->getBasename('.'.$fileInfo->getExtension());

    if($template->hasBlock('patterninfo')) {
      $info = $template->renderBlock('patterninfo');
      $info = Yaml::parse($info);
      $info += [
        'id' => $basename,
        'name' => ucfirst($basename),
        'tags' => [],
      ];
      return $this->createPattern($info, $fileInfo->getPathname());
    }
  }

  private function createPattern(array $data, $filename) {
    assert(isset($data['id']), 'Id is set');
    assert(isset($data['name']), 'Name is set');
    assert(isset($data['tags']), 'Tags is an array');

    $pattern = new TwigPattern($data['id'], $data['name'], $filename);
    if(is_array($data['tags'])) {
      foreach($data['tags'] as $name => $value) {
        $pattern->addTag($name, $value);
      }
    }

    return $pattern;
  }
}