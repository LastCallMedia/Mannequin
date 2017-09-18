<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Tests\Component;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Tests\Component\ComponentTestCase;
use LastCall\Mannequin\Html\Component\HtmlComponent;

class HtmlComponentTest extends ComponentTestCase
{
    public function getPattern(): ComponentInterface
    {
        return new HtmlComponent(
            self::PATTERN_ID,
            self::PATTERN_ALIASES,
            new \SplFileInfo(self::TEMPLATE_FILE)
        );
    }
}
