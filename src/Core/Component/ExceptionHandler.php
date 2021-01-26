<?php
/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Component;


use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\ErrorHandler\ExceptionHandler as DebugExceptionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{
    protected $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function onMannequinError(ExceptionEvent $event)
    {
        $handler = new DebugExceptionHandler($this->debug);

        $exception = $event->getThrowable();
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        $response = Response::create($handler->getHtml($exception), $exception->getStatusCode(), $exception->getHeaders())->setCharset(ini_get('default_charset'));

        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => ['onMannequinError', -255]];
    }
}
