<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Local Development UI.
 *
 * This class is used to proxy UI file requests to the live development server
 * (webpack).
 */
class LocalDevelopmentUi extends LocalUi
{
    private $parts;

    public function __construct($url)
    {
        $this->parts = parse_url($url);
    }

    public function files(): array
    {
        throw new \Exception('Development UI files cannot be listed.');
    }

    public function isUiFile(string $path): bool
    {
        return preg_match(
            '@^(static/|sockjs-node/|index.html$|favicon.ico$)@',
            $path
        );
    }

    public function getUiFileResponse(string $path, Request $request): Response
    {
        $parts = parse_url($request->getUri());
        $parts = $this->parts + $parts;

        // Reassemble the URI.
        $uri = sprintf(
            '%s://%s:%s%s',
            $parts['scheme'],
            $parts['host'],
            $parts['port'],
            $parts['path']
        );
        if (!empty($parts['query'])) {
            $uri .= sprintf('?%s', $parts['query']);
        }
        if ($path === 'index.html') {
            $ctx = stream_context_create([
                'http' => ['timeout' => 5],
            ]);
            // The index cannot be served with a redirect.
            return new Response(file_get_contents($uri, false, $ctx));
        }

        return new RedirectResponse($uri, 307);
    }
}
