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

interface UiInterface
{
    /**
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function files(): \Traversable;

    public function isUiFile(string $path): bool;

    public function getUiFileResponse(string $path, Request $request): Response;

    public function decorateRendered(Rendered $rendered): string;
}
