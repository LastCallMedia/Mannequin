<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Asset\AssetManagerInterface;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticFileController
{
    private $ui;
    private $assetManager;

    public function __construct(UiInterface $ui, AssetManagerInterface $assetManager)
    {
        $this->ui = $ui;
        $this->assetManager = $assetManager;
    }

    public function indexAction(Request $request): Response
    {
        return $this->ui->getIndexFileResponse($request);
    }

    public function staticAction($name, Request $request): Response
    {
        // Check if this is one of the UI files.
        if ($this->ui->isUiFile($name)) {
            return $this->ui->getUiFileResponse($name, $request);
        }
        // Let the AssetManager to handle the request.
        return new BinaryFileResponse($this->assetManager->get($name));
    }
}
