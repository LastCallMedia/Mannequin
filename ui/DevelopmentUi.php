<?php


namespace LastCall\Mannequin\Ui;


use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DevelopmentUi implements UiInterface {

  public function __construct($url) {
    $this->parts = parse_url($url);
  }

  public function files(): array {
    throw new \Exception('Development UI files cannot be listed.');
  }

  public function isUiFile(string $path): bool {
    return preg_match('@^(static/|sockjs-node/|index.html$)@', $path);
  }

  public function getUiFileResponse(string $path, Request $request): Response {
    $parts = parse_url($request->getUri());
    $parts = $this->parts + $parts;

    // Reassemble the URI.
    $uri = sprintf('%s://%s:%s%s', $parts['scheme'], $parts['host'], $parts['port'], $parts['path']);
    if(!empty($parts['query'])) {
      $uri.= sprintf('?%s', $parts['query']);
    }
    if($path === 'index.html') {
      // The index cannot be served with a redirect.
      return new Response(file_get_contents($uri));
    }

    return new RedirectResponse($uri, 307);
  }
}