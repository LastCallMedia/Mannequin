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

use LastCall\Mannequin\Core\Ui\UiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StaticFileController
{
    private $ui;

    public function __construct(UiInterface $ui, string $assetDir)
    {
        $this->ui = $ui;
        $this->assetDir = $assetDir;
    }

    public function indexAction(Request $request): Response
    {
        return $this->ui->getIndexFileResponse($request);
    }

    public function staticAction($name, Request $request): Response
    {
        if ($this->ui->isUiFile($name)) {
            return $this->ui->getUiFileResponse($name, $request);
        }

        // Check if this file is an asset we already know:
        if (file_exists($this->assetDir.'/'.$name)) {
            return new BinaryFileResponse($this->assetDir.'/'.$name);
        }

        throw new NotFoundHttpException(sprintf('Asset not found: %s.  Checked in %s', $name, $this->assetDir));
    }
}
