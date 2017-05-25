<?php

namespace LastCall\Mannequin\Cli\Ui;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UiWriter {

  public function __construct(UiRenderer $renderer, UrlGeneratorInterface $generator) {
    $this->renderer = $renderer;
    $this->generator = $generator;
    $this->context = new Request();
    $this->filesystem = new Filesystem();
  }

  public function prepare($dir) {
    $this->filesystem->mkdir(sprintf('%s/%s', $dir, '_render'));
  }

  public function writeManifest(PatternCollection $collection, $dir) {
    $manifest = $this->renderer->renderManifest($collection, $this->generator);
    $manifest_path = $this->generator->generate('manifest', [], UrlGeneratorInterface::RELATIVE_PATH);
    file_put_contents(sprintf('%s/%s', $dir, $manifest_path), json_encode($manifest, JSON_PRETTY_PRINT));
  }

  public function writeRender(PatternInterface $pattern, $dir) {
    $rendered = $this->renderer->renderPattern($pattern);
    $rendered_path = $this->generator->generate('pattern_render', ['pattern' => $pattern->getId()], UrlGeneratorInterface::RELATIVE_PATH);
    file_put_contents(sprintf('%s/%s', $dir, $rendered_path), $rendered);
  }

  public function writeAssets($mapping, $dir) {
    foreach($mapping as $src => $dest) {
      $this->filesystem->symlink($dest, sprintf('%s/%s', $dir, $src));
    }
  }

  public function writeUi($dir) {
    $this->filesystem->mirror(__DIR__.'/../../ui/build', $dir, NULL, ['override' => TRUE]);
  }
}