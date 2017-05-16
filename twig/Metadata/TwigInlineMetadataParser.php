<?php


namespace LastCall\Patterns\Twig\Metadata;


use LastCall\Patterns\Core\Metadata\YamlFileMetadataParser;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableFactoryInterface;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use Symfony\Component\Yaml\Yaml;

class TwigInlineMetadataParser extends YamlFileMetadataParser {

  private $twig;

  public function __construct(\Twig_Environment $twig, VariableFactoryInterface $variableFactory) {
    $this->twig = $twig;
    $this->variableFactory = $variableFactory;
  }

  public function hasMetadata(PatternInterface $pattern): bool {
    if($pattern instanceof TwigPattern) {
      $template = $this->twig->load($pattern->getSource()->getName());
      return $template->hasBlock('patterninfo');
    }
  }

  public function getMetadata(PatternInterface $pattern): array {
    if($pattern instanceof TwigPattern) {
      $template = $this->twig->load($pattern->getSource()->getName());
      if($template->hasBlock('patterninfo')) {
        $yaml = $template->renderBlock('patterninfo');
        $yaml = Yaml::parse($yaml);
        return $this->processMetadata($yaml);
      }
    }
    throw new \InvalidArgumentException(sprintf('Unable to parse metadata for %s', $pattern->getId()));
  }
}