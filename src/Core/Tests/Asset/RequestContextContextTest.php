<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Asset;

use LastCall\Mannequin\Core\Asset\RequestContextContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;

class RequestContextContextTest extends TestCase
{
    public function getBaseUrlTests()
    {
        return [
            ['/', '/foo', '.'],
            ['/', '/foo/', '..'],
            ['/foo', '/foo', ''],
            ['/foo', '/foo/bar', '../foo'],
        ];
    }

    /**
     * @dataProvider getBaseUrlTests
     */
    public function testBaseUrl($base, $current, $expected)
    {
        $requestContext = $this->prophesize(RequestContext::class);
        $requestContext->getPathInfo()->willReturn($current);
        $requestContext->getBaseUrl()->willReturn($base);
        $context = new RequestContextContext($requestContext->reveal());
        $this->assertEquals($expected, $context->getBasePath());
    }
}
