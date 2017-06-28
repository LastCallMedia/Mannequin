<?php

namespace LastCall\Mannequin\Core\Ui;

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
    foreach($pattern->getVariableSets() as $setId => $set) {
      $rendered = $this->renderer->renderPattern($pattern, $set);
      $rendered_path = $this->generator->generate('pattern_render', ['pattern' => $pattern->getId(), 'set' => $setId], UrlGeneratorInterface::RELATIVE_PATH);
      $this->filesystem->mkdir(sprintf('%s/%s', $dir, dirname($rendered_path)));
      file_put_contents(sprintf('%s/%s', $dir, $rendered_path), $rendered);
    }
  }

  public function writeSource(PatternInterface $pattern, $dir) {
    $raw = $this->renderer->renderSourceRaw($pattern);
    $raw_path = $this->generator->generate('pattern_render_source_raw', ['pattern' => $pattern->getId()], UrlGeneratorInterface::RELATIVE_PATH);
    $this->filesystem->mkdir(sprintf('%s/%s', $dir, dirname($raw_path)));
    file_put_contents(sprintf('%s/%s', $dir, $raw_path), $raw);

    foreach($pattern->getVariableSets() as $setId => $set) {
      $raw = $this->renderer->renderPatternRaw($pattern, $set);
      $raw_path = $this->generator->generate('pattern_render_raw', ['pattern' => $pattern->getId(), 'set' => $setId], UrlGeneratorInterface::RELATIVE_PATH);
      $this->filesystem->mkdir(sprintf('%s/%s', $dir, dirname($raw_path)));
      file_put_contents(sprintf('%s/%s', $dir, $raw_path), $raw);
    }
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