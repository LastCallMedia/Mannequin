<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Console\Command;

use LastCall\Mannequin\Core\Asset\AssetManager;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Console\Command\SnapshotCommand;
use LastCall\Mannequin\Core\Discovery\ExplicitDiscovery;
use LastCall\Mannequin\Core\Snapshot\CameraInterface;
use LastCall\Mannequin\Core\Snapshot\Snapshot;
use LastCall\Mannequin\Core\Snapshot\SnapshotWriterInterface;
use LastCall\Mannequin\Core\Tests\Stubs\TestComponent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Tester\CommandTester;

class SnapshotCommandTest extends TestCase
{
    public function testReturns0OnSuccess()
    {
        $collection = new ComponentCollection([]);
        $am = new AssetManager(new \ArrayIterator([]), '');
        $snapshot = new Snapshot(new \ArrayIterator([]));

        $camera = $this->prophesize(CameraInterface::class);
        $camera
            ->snapshot($collection, $am, Argument::type('callable'))
            ->shouldBeCalled()
            ->willReturn($snapshot);
        $discovery = new ExplicitDiscovery($collection);

        $writer = $this->prophesize(SnapshotWriterInterface::class);
        $writer
            ->write($snapshot)
            ->shouldBeCalled();

        $command = new InjectableWriterSnapshotCommand(
            'snapshot',
            $camera->reveal(),
            $discovery,
            $am
        );
        $command->setWriterFactory(function () use ($writer) {
            return $writer->reveal();
        });
        $tester = new CommandTester($command);
        $this->assertEquals(0, $tester->execute([]));
    }

    public function testReturns1OnComponentRenderFailure()
    {
        $collection = new ComponentCollection([]);
        $am = new AssetManager(new \ArrayIterator([]), '');

        $camera = $this->prophesize(CameraInterface::class);
        $camera
            ->snapshot($collection, $am, Argument::type('callable'))
            ->will(function ($args) {
                $component = new TestComponent('foo');
                $component->setName('Foo');
                $args[2](new \RuntimeException('test'), $component);

                return new Snapshot(new \ArrayIterator([]));
            });
        $discovery = new ExplicitDiscovery($collection);
        $writer = $this->prophesize(SnapshotWriterInterface::class);

        $command = new InjectableWriterSnapshotCommand(
            'snapshot',
            $camera->reveal(),
            $discovery,
            $am
        );
        $command->setWriterFactory(function () use ($writer) {
            return $writer;
        });
        $tester = new CommandTester($command);
        $this->assertEquals(1, $tester->execute([]));
        $this->assertContains('Caught exception generating snapshot for Foo: test', $tester->getDisplay());
    }
}

class InjectableWriterSnapshotCommand extends SnapshotCommand
{
    public function setWriterFactory(callable $factory)
    {
        $this->writerFactory = $factory;
    }
}
