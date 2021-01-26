<?php
/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\EventListener;


use LastCall\Mannequin\Core\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MiddlewareListener implements EventSubscriberInterface
{
    protected $app;

    /**
     * Constructor.
     *
     * @param Application $app An Application instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Runs before filters.
     *
     * @param ResponseEvent $event The event to handle
     */
    public function onKernelRequest(ResponseEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (!$route = $this->app['routes']->get($routeName)) {
            return;
        }

        foreach ((array) $route->getOption('_before_middlewares') as $callback) {
            $ret = call_user_func($this->app['callback_resolver']->resolveCallback($callback), $request, $this->app);
            if ($ret instanceof Response) {
                $event->setResponse($ret);

                return;
            } elseif (null !== $ret) {
                throw new \RuntimeException(sprintf('A before middleware for route "%s" returned an invalid response value. Must return null or an instance of Response.', $routeName));
            }
        }
    }

    /**
     * Runs after filters.
     *
     * @param ControllerEvent $event The event to handle
     */
    public function onKernelResponse(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (!$route = $this->app['routes']->get($routeName)) {
            return;
        }

        foreach ((array) $route->getOption('_after_middlewares') as $callback) {
            $response = call_user_func($this->app['callback_resolver']->resolveCallback($callback), $request, $event->getResponse(), $this->app);
            if ($response instanceof Response) {
                $event->setResponse($response);
            } elseif (null !== $response) {
                throw new \RuntimeException(sprintf('An after middleware for route "%s" returned an invalid response value. Must return null or an instance of Response.', $routeName));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // this must be executed after the late events defined with before() (and their priority is -512)
            KernelEvents::REQUEST => ['onKernelRequest', -1024],
            KernelEvents::RESPONSE => ['onKernelResponse', 128],
        ];
    }
}
