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


use LastCall\Mannequin\Core\AppArgumentValueResolver;
use LastCall\Mannequin\Core\CallbackResolver;
use LastCall\Mannequin\Core\EventListener\ConverterListener;
use LastCall\Mannequin\Core\EventListener\EventListenerProviderInterface;
use LastCall\Mannequin\Core\EventListener\MiddlewareListener;
use LastCall\Mannequin\Core\EventListener\StringToResponseListener;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\WebLink\EventListener\AddLinkHeaderListener;
use Symfony\Component\WebLink\HttpHeaderSerializer;

class HttpKernelServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
        $app['resolver'] = function ($app) {
            return new ControllerResolver($app['logger']);
        };

        $app['argument_metadata_factory'] = function ($app) {
            return new ArgumentMetadataFactory();
        };
        $app['argument_value_resolvers'] = function ($app) {
            return array_merge([new AppArgumentValueResolver($app)], ArgumentResolver::getDefaultArgumentValueResolvers());
        };

        $app['argument_resolver'] = function ($app) {
            return new ArgumentResolver($app['argument_metadata_factory'], $app['argument_value_resolvers']);
        };

        $app['kernel'] = function ($app) {
            return new HttpKernel($app['dispatcher'], $app['resolver'], $app['request_stack'], $app['argument_resolver']);
        };

        $app['request_stack'] = function () {
            return new RequestStack();
        };

        $app['dispatcher'] = function () {
            return new EventDispatcher();
        };

        $app['callback_resolver'] = function ($app) {
            return new CallbackResolver($app);
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new ResponseListener($app['charset']));
        $dispatcher->addSubscriber(new MiddlewareListener($app));
        $dispatcher->addSubscriber(new ConverterListener($app['routes'], $app['callback_resolver']));
        $dispatcher->addSubscriber(new StringToResponseListener());

        if (class_exists(HttpHeaderSerializer::class)) {
            $dispatcher->addSubscriber(new AddLinkHeaderListener());
        }
    }
}
