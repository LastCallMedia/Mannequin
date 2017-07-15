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

class UiController
{
    private $ui;

    public function __construct(UiInterface $ui)
    {
        $this->ui = $ui;
    }

    public function staticAction($name, Request $request): Response
    {
        if ($this->ui->isUiFile($name)) {
            return $this->ui->getUiFileResponse($name, $request);
        }
        // @todo: Assets need to be checked here.
        throw new NotFoundHttpException('Asset not found.');
    }
}
