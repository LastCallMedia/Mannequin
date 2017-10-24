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

use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wraps a UI class to check that the local files exist before trying to use
 * them.
 */
class CheckingUiDecorator implements UiInterface
{
    private $inner;

    private $source;

    private $destination;

    private $checked;

    public function __construct(
        UiInterface $inner,
        string $source,
        string $destination
    ) {
        $this->inner = $inner;
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function files(): \Traversable
    {
        $this->checkDownloaded();

        return $this->inner->files();
    }

    public function isUiFile(string $path): bool
    {
        $this->checkDownloaded();

        return $this->inner->isUiFile($path);
    }

    public function getIndexFileResponse(Request $request): Response
    {
        $this->checkDownloaded();

        return $this->inner->getIndexFileResponse($request);
    }

    public function getUiFileResponse(string $path, Request $request): Response
    {
        $this->checkDownloaded();

        return $this->inner->getUiFileResponse($path, $request);
    }

    public function decorateRendered(Rendered $rendered): string
    {
        $this->checkDownloaded();

        return $this->inner->decorateRendered($rendered);
    }

    private function checkDownloaded()
    {
        if (null === $this->checked) {
            $this->checked = is_dir($this->destination);
        }
        if (false === $this->checked) {
            throw new \RuntimeException(sprintf(
                'Unable to find UI files.  This usually happens automatically, but didn\'t, for some reason.  Please download them from %s and place them at %s, and file a bug report.',
                $this->source,
                $this->destination
            ));
        }
    }
}
