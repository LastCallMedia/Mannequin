<?php


namespace LastCall\Mannequin\Ui;


use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Ui implements UiInterface {

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

  public function files(): array {
    $manifest = file_get_contents(__DIR__.'/build/asset-manifest.json');
    $files = [];
    foreach(json_decode($manifest, TRUE) as $file) {
      $files[$file] = $this->uiPath($file);
    }
    $files['index.html'] = $this->uiPath('index.html');
    return $files;
  }

  public function isUiFile(string $path): bool {
    return file_exists($this->uiPath($path));
  }

  public function getUiFileResponse(string $path, Request $request) : Response {
    return new BinaryFileResponse($this->uiPath($path));
  }

  public function decorateRendered(Rendered $rendered): string {
    return sprintf(self::TEMPLATE,
      $this->mapAssets($rendered->getScripts(), '<script type="text/javascript" src="%s"></script>'),
      $this->mapAssets($rendered->getStyles(), '<link rel="stylesheet" href="%s" />'),
      $rendered->getMarkup()
    );
  }
  
  private function uiPath($relativePath = '') {
    return rtrim(sprintf('%s/build/%s', __DIR__, $relativePath), '/');
  }

  private function mapAssets(array $assets, $pattern) {
    return implode("\n", array_map(function($asset) use ($pattern) {
      return sprintf($pattern, $asset);
    }, $assets));
  }
}