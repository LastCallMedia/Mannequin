<?php


namespace LastCall\Mannequin\Cli\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AssetController {

  private $mappings;

  public function __construct($assetMappings) {
    $this->mappings = $assetMappings;
  }

  /**
   * Given a URL, try to map it to a filesystem path using the asset mapping.
   *
   * @todo: This is insecure.  Assert the asset is within the asset directory.
   *
   * @param $url
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function getAssetAction($url) {
    foreach($this->mappings as $urlPart => $path) {
      if(strpos($url, $urlPart) === 0) {
        $searchPath = $path . '/' . ltrim(substr($url, strlen($urlPart)), '/');
        if(is_file($searchPath) && is_readable($searchPath)) {
          return new BinaryFileResponse(new \SplFileInfo($searchPath));
        }
      }
    }
    throw new NotFoundHttpException('Unknown asset.');
  }
}