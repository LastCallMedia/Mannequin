<?php


namespace LastCall\Mannequin\Cli\Writer;


use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface;

class UiWriter {

  public function __construct(EngineInterface $engine) {
    $this->templating = $engine;
    $this->filesystem = new Filesystem();
  }

  private function getRenderPath(PatternInterface $pattern) {
    return sprintf('_render/%s.html', md5($pattern->getId()));
  }

  public function writeAll(ConfigInterface $config, $path) {
    $renderer = $config->getRenderer();
    $labeller = $config->getLabeller();

    if(!$this->filesystem->exists($path)) {
      $this->filesystem->mkdir($path);
    }
    if(!$this->filesystem->exists($path .'/_render')) {
      $this->filesystem->mkdir($path.'/_render');
    }

    foreach($config->getCollection() as $pattern) {
      $rendered = $renderer->render($pattern);
      $filename = sprintf('%s/%s', $path, $this->getRenderPath($pattern));
      file_put_contents($filename, $this->templating->render('rendered.html.php', [
        'title' => $labeller->getPatternLabel($pattern),
        'markup' => $rendered->getMarkup(),
        'styles' => $rendered->getStyles(),
        'scripts' => $rendered->getScripts(),
      ]));
    }
    $this->writeManifest($config, $path);
    $this->writeIndex($config, $path);
    $this->writeAssets($config, $path);
  }

  public function writeManifest(ConfigInterface $config, $path) {
    $labeller = $config->getLabeller();
    if(!$this->filesystem->exists($path)) {
      $this->filesystem->mkdir($path);
    }
    $patterns = [];
    foreach($config->getCollection() as $pattern) {
      $patterns[] = [
        'id' => $pattern->getId(),
        'rendered' => $this->getRenderPath($pattern),
        'name' => $labeller->getPatternLabel($pattern),
        'tags' => $pattern->getTags(),
      ];
    }
    $output = json_encode($patterns, JSON_PRETTY_PRINT);
    file_put_contents($path.'/manifest.json', $output);
  }

  public function writeIndex(ConfigInterface $config, $path) {
    $output = $this->templating->render('index.html.php');
    file_put_contents($path.'/index.php', $output);
    file_put_contents($path.'/index.html', $output);
  }

  public function writeAssets(ConfigInterface $config, $rootPath) {
    foreach($config->getAssetMappings() as $url => $path) {
      $this->filesystem->symlink($path, sprintf('%s/%s', $rootPath, $url));
    }
  }
}