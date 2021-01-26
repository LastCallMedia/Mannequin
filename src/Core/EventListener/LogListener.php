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

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Logs request, response, and exceptions.
 */
class LogListener implements EventSubscriberInterface
{
    protected $logger;
    protected $exceptionLogFilter;

    public function __construct(LoggerInterface $logger, $exceptionLogFilter = null)
    {
        $this->logger = $logger;
        if (null === $exceptionLogFilter) {
            $exceptionLogFilter = function (\Exception $e) {
                if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
                    return LogLevel::ERROR;
                }

                return LogLevel::CRITICAL;
            };
        }

        $this->exceptionLogFilter = $exceptionLogFilter;
    }

    /**
     * Logs master requests on event KernelEvents::REQUEST.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logRequest($event->getRequest());
    }

    /**
     * Logs master response on event KernelEvents::RESPONSE.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logResponse($event->getResponse());
    }

    /**
     * Logs uncaught exceptions on event KernelEvents::EXCEPTION.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->logException($event->getException());
    }

    /**
     * Logs a request.
     */
    protected function logRequest(Request $request)
    {
        $this->logger->log(LogLevel::DEBUG, '> '.$request->getMethod().' '.$request->getRequestUri());
    }

    /**
     * Logs a response.
     */
    protected function logResponse(Response $response)
    {
        $message = '< '.$response->getStatusCode();

        if ($response instanceof RedirectResponse) {
            $message .= ' '.$response->getTargetUrl();
        }

        $this->logger->log(LogLevel::DEBUG, $message);
    }

    /**
     * Logs an exception.
     */
    protected function logException(\Exception $e)
    {
        $this->logger->log(call_user_func($this->exceptionLogFilter, $e), sprintf('%s: %s (uncaught exception) at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()), ['exception' => $e]);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
            /*
             * Priority -4 is used to come after those from SecurityServiceProvider (0)
             * but before the error handlers added with Silex\Application::error (defaults to -8)
             */
            KernelEvents::EXCEPTION => ['onKernelException', -4],
        ];
    }
}
