<?php

namespace LastCall\Mannequin\Core\Tests\Console;

use LastCall\Mannequin\Core\Console\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testHasDebugOption()
    {
        $definition = (new Application())->getDefinition();

        $this->assertTrue($definition->hasOption('debug'));
        $this->assertFalse(
            $definition->getOption('debug')->getDefault()
        );
    }

    public function testHasConfigOption()
    {
        $definition = (new Application())->getDefinition();

        $this->assertTrue($definition->hasOption('debug'));
        $this->assertFalse(
            $definition->getOption('debug')->getDefault()
        );
    }
}
