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

class UiController
{
    private $ui;

    public function __construct(UiInterface $ui, array $assetMappings)
    {
        $this->ui = $ui;
        $this->assetMappings = $assetMappings;
    }

    public function staticAction($name, Request $request): Response
    {
        if ($this->ui->isUiFile($name)) {
            return $this->ui->getUiFileResponse($name, $request);
        }

        // Check if the file exists in our asset map.
        foreach ($this->assetMappings as $urlPart => $pathPart) {
            if (strpos($name, $urlPart) !== false) {
                $end = substr($name, strlen($urlPart));

                if (file_exists($pathPart.'/'.$end)) {
                    return new BinaryFileResponse($pathPart.'/'.$end);
                }
            }
        }

        throw new NotFoundHttpException(sprintf('Asset not found: %s', $name));
    }
}
