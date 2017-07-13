<?php


namespace LastCall\Mannequin\Core\Ui;


class HtmlDecorator {

  const TEMPLATE = <<<'EOD'
<html>
<head>
  %s
  %s
</head>
<body>
  %s
</body>
EOD;

  public function decorate($body, array $scripts = [], array $styles = []) {
    return sprintf(self::TEMPLATE,
      $this->mapAssets($scripts, '<script type="text/javascript" src="%s"></script>'),
      $this->mapAssets($styles, '<link rel="stylesheet" href="%s" />'),
      $body
    );
  }

  private function mapAssets(array $assets, $pattern) {
    return implode("\n", array_map(function($asset) use ($pattern) {
      return sprintf($pattern, $asset);
    }, $assets));
  }

}