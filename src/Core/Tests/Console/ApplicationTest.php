<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Console;

use LastCall\Mannequin\Core\Console\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testHasDebugOption()
    {
        $definition = (new Application(''))->getDefinition();

        $this->assertTrue($definition->hasOption('debug'));
        $this->assertFalse(
            $definition->getOption('debug')->getDefault()
        );
    }

    public function testHasConfigOption()
    {
        $definition = (new Application(''))->getDefinition();

        $this->assertTrue($definition->hasOption('debug'));
        $this->assertFalse(
            $definition->getOption('debug')->getDefault()
        );
    }
}
