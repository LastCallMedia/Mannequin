<?php

namespace LastCall\Mannequin\Html\Discovery;

use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;

class HtmlDiscovery implements DiscoveryInterface {
  use IdEncoder;

  private $files;

  public function __construct(\Traversable $files) {
    $this->files = $files;
  }

  public function discover(): PatternCollection {
    $patterns = [];
    foreach($this->files as $filenames) {
      if(!is_array($filenames)) {
        $filenames = [$filenames];
      }
      $filenames = array_map(function($filename) {
        return (string) $filename;
      }, $filenames);

      $id = reset($filenames);
      $pattern = new HtmlPattern($this->encodeId($id), $filenames, new \SplFileInfo($id));
      $pattern->addTag('format', 'html');
      $patterns[] = $pattern;
    }
    return new PatternCollection($patterns);
  }

}