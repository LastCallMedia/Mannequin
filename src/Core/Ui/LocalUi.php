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
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Local UI.
 *
 * This class depends on precompiled UI files being available on the filesystem.
 */
class LocalUi implements UiInterface
{
    const TEMPLATE = <<<'EOD'
<html>
<head>
  %s
  %s
</head>
<body>
  %s
</body>
EOD;

    private $uiPath;

    public function __construct(string $uiPath)
    {
        $this->uiPath = $uiPath;
    }

    /**
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function files(): \Traversable
    {
        return Finder::create()->in($this->uiPath())->files();
    }

    protected function uiPath($relativePath = '')
    {
        return rtrim(sprintf('%s/build/%s', $this->uiPath, $relativePath), '/');
    }

    public function isUiFile(string $path): bool
    {
        return file_exists($this->uiPath(ltrim($path, '/')));
    }

    public function getUiFileResponse(string $path, Request $request): Response
    {
        return new BinaryFileResponse($this->uiPath(ltrim($path, '/')));
    }

    public function getIndexFileResponse(Request $request): Response
    {
        return new BinaryFileResponse($this->uiPath('index.html'));
    }

    public function decorateRendered(Rendered $rendered): string
    {
        return sprintf(
            self::TEMPLATE,
            $this->mapAssets(
                $rendered->getJs(),
                '<script type="text/javascript" src="%s"></script>'
            ),
            $this->mapAssets(
                $rendered->getCss(),
                '<link rel="stylesheet" href="%s" />'
            ),
            $rendered->getMarkup()
        );
    }

    private function mapAssets($assets, $component)
    {
        $tags = [];
        foreach ($assets as $asset) {
            $tags[] = sprintf($component, $asset);
        }

        return implode("\n", $tags);
    }
}
