<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Asset;

use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;

/**
 * Returns a request-relative base URL (ex: ../../assets).
 */
class RequestContextContext implements ContextInterface
{
    private $context;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function getBasePath()
    {
        return rtrim(UrlGenerator::getRelativePath($this->context->getPathInfo(), $this->context->getBaseUrl()), '/');
    }

    public function isSecure()
    {
        return $this->context->getScheme() === 'https';
    }
}
