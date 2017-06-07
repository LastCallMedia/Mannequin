<?php


namespace LastCall\Mannequin\Core\Ui\Controller;


use LastCall\Mannequin\Core\Ui\UiRenderer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UiController {

  public function __construct(UrlGeneratorInterface $generator, UiRenderer $renderer) {
    $this->generator = $generator;
    $this->renderer = $renderer;
  }

  public function indexAction() {
    return $this->getUiFile('index.html');
  }

  public function staticAction($name) {
    if(strpos($name, 'static/') === 0) {
      return $this->getUiFile($name);
    }
    // @todo: Assets need to be checked here.
    return new Response($name);
  }

  private function getUiFile($name) {
    $filename = sprintf(__DIR__.'/../../../ui/build/%s', $name);
    if(file_exists($filename)) {
      return new BinaryFileResponse($filename);
    }
    throw new NotFoundHttpException('File not found.');
  }

}