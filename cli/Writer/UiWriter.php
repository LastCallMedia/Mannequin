<?php


namespace LastCall\Mannequin\Cli\Writer;


use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Labeller;
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

  private function getSourcePath(PatternInterface $pattern) {
    return sprintf('_source/%s.txt', md5($pattern->getId()));
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
    if(!$this->filesystem->exists($path .'/_source')) {
      $this->filesystem->mkdir($path.'/_source');
    }

    foreach($config->getCollection() as $pattern) {
      $source = $renderer->renderSource($pattern);
      $rendered = $renderer->render($pattern);
      $filename = sprintf('%s/%s', $path, $this->getRenderPath($pattern));
      file_put_contents($filename, $this->templating->render('rendered.html.php', [
        'title' => $labeller->getPatternLabel($pattern),
        'markup' => $rendered->getMarkup(),
        'styles' => $rendered->getStyles(),
        'scripts' => $rendered->getScripts(),
      ]));
      $sourceFilename = sprintf('%s/%s', $path, $this->getSourcePath($pattern));
      file_put_contents($sourceFilename, $source);
    }
    $this->writeManifest($config, $path);
    $this->writeIndex($config, $path);
    $this->writeAssets($config, $path);
    $this->writeUi($path);
  }

  public function writeManifest(ConfigInterface $config, $path) {
    $labeller = $config->getLabeller();
    if(!$this->filesystem->exists($path)) {
      $this->filesystem->mkdir($path);
    }
    $patterns = $tags = [];
    foreach($config->getCollection() as $pattern) {
      $patterns[] = [
        'id' => md5($pattern->getId()),
        'rendered' => $this->getRenderPath($pattern),
        'source' => $this->getSourcePath($pattern),
        'name' => $labeller->getPatternLabel($pattern),
        'description' => $pattern->getDescription(),
        'tags' => $pattern->getTags(),
      ];
      $tags = array_merge($tags, $this->collectPatternTags($pattern, $labeller));
    }
    $seen = [];
    $tags = array_filter($tags, function($tag) use (&$seen) {
      if(!isset($seen[$tag['id']])) {
        $seen[$tag['id']] = TRUE;
        return TRUE;
      }
      return FALSE;
    });

    $output = json_encode([
      'patterns' => $patterns,
      'tags' => array_values($tags),
    ], JSON_PRETTY_PRINT);
    file_put_contents($path.'/manifest.json', $output);
  }

  public function writeIndex(ConfigInterface $config, $path) {
    $output = $this->templating->render('index.html.php');
    file_put_contents($path.'/index.php', $output);
  }

  public function writeAssets(ConfigInterface $config, $rootPath) {
    foreach($config->getAssetMappings() as $url => $path) {
      $this->filesystem->symlink($path, sprintf('%s/%s', $rootPath, $url));
    }
  }

  public function writeUi($rootPath) {
    $this->filesystem->mirror(realpath(__DIR__.'/../../ui/build'), $rootPath, NULL, ['override' => TRUE]);
  }

  private function collectPatternTags(PatternInterface $pattern, Labeller $labeller) {
    $tags = [];
    foreach($pattern->getTags() as $k=> $v) {
      $tags[] = [
        'id' => md5($k.':'.$v),
        'type' => $k,
        'value' => $v,
        'name' => $labeller->getTagLabel($k, $v)
      ];
    }
    return $tags;
  }
}