<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Route;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Security trait.
 */
trait SecurityTrait
{
    public function secure($roles)
    {
        $this->before(function ($request, $app) use ($roles) {
            if (!$app['security.authorization_checker']->isGranted($roles)) {
                throw new AccessDeniedException();
            }
        });
    }
}
