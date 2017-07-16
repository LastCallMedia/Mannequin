<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Tests\Extension;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Html\Extension\HtmlExtension;
use Symfony\Component\Finder\Finder;

class HtmlExtensionTest extends ExtensionTestCase
{
    public function getExtension(): ExtensionInterface
    {
        return new HtmlExtension(
            [
                'finder' => Finder::create()->in([__DIR__]),
            ]
        );
    }
}
