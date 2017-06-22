<?php


namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Ui\UiRenderer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UiController {

  private $generator;
  private $renderer;
  private $uiServer;

  public function __construct(UrlGeneratorInterface $generator, UiRenderer $renderer, $uiServer = NULL) {
    $this->generator = $generator;
    $this->renderer = $renderer;
    if($uiServer) {
      $this->uiServer = parse_url($uiServer) + [
        'scheme' => 'http',
        'port' => 80,
      ];
    }
  }

  public function indexAction(Request $request) {
    return $this->getUiFile('index.html', $request);
  }

  public function staticAction($name, Request $request) {
    if(preg_match('@^(static/|sockjs-node/)@', $name)) {
      return $this->getUiFile($name, $request);
    }
    // @todo: Assets need to be checked here.
    throw new NotFoundHttpException('Asset not found.');
  }

  private function getUiFile($name, Request $request) {
    if($this->uiServer) {
      $response = $this->fetchUiFileFromServer($name, $request);
    }
    else {
      $response = $this->fetchUiFileFromDirectory($name);
    }

    return $response ?: new NotFoundHttpException('File not found.');
  }

  private function fetchUiFileFromDirectory($name) {
    $filename = sprintf(__DIR__.'/../../../ui/build/%s', $name);
    if(file_exists($filename)) {
      return new BinaryFileResponse($filename);
    }
  }

  /**
   * This method fetches the UI file from a live development server.
   */
  private function fetchUiFileFromServer($name, Request $request) {
    $parts = parse_url($request->getUri());

    $parts['scheme'] = $this->uiServer['scheme'];
    $parts['host'] = $this->uiServer['host'];
    $parts['port'] = $this->uiServer['port'];

    // Reassemble the URI.
    $uri = sprintf('%s://%s:%s%s', $parts['scheme'], $parts['host'], $parts['port'], $parts['path']);
    if(!empty($parts['query'])) {
      $uri.= sprintf('?%s', $parts['query']);
    }

    if($name !== 'index.html') {
      return new RedirectResponse($uri, 307);
    }
    return new Response(file_get_contents($uri));
  }

}