<?php


namespace LastCall\Patterns\Core\Extension;


use LastCall\Patterns\Core\Parser\HtmlTemplateParser;
use LastCall\Patterns\Core\Render\HtmlRenderer;

class HtmlExtension extends AbstractExtension {

  public function getParsers(): array {
    return [ new HtmlTemplateParser()];
  }

  public function getRenderers(): array {
    $config = $this->getConfig();
    return [ new HtmlRenderer($config->getStyles(), $config->getScripts()) ];
  }
}