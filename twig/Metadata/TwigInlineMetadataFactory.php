<?php


namespace LastCall\Mannequin\Twig\Metadata;


use LastCall\Mannequin\Core\Metadata\YamlFileMetadataFactory;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use Symfony\Component\Yaml\Yaml;

class TwigInlineMetadataFactory extends YamlFileMetadataFactory {

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