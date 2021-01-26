<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Provider;

use LastCall\Mannequin\Core\ServiceControllerResolver;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceControllerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend('resolver', function ($resolver, $app) {
            return new ServiceControllerResolver($resolver, $app['callback_resolver']);
        });
    }
}
