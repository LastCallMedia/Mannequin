<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Subscriber;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\TemplateFileInterface;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class LastChanceNameSubscriberTest extends TestCase
{
    use DiscoverySubscriberTestTrait;

    public function getFilenameTests()
    {
        return [
            ['/foo/bar/foo.txt', 'Foo'],
            ['/foo/bar/_foo.txt', 'Foo'],
            ['/foo/bar/foo.html.twig', 'Foo'],
            ['/foo/bar/foo-bar.txt', 'Foo bar'],
            ['/foo/bar/foo_bar.txt', 'Foo bar'],
        ];
    }

    /**
     * @dataProvider getFilenameTests
     */
    public function testCreatesNameFromFileName($filename, $expectedName)
    {
        $pattern = $this->prophesize(TemplateFileInterface::class);
        $pattern->getName()->willReturn('');
        $pattern->setName($expectedName)->shouldBeCalled();
        $pattern->getFile()->willReturn(new \SplFileInfo($filename));
        $this->dispatchDiscover(
            new LastChanceNameSubscriber(),
            $pattern->reveal()
        );
    }

    public function testFallsBackToId()
    {
        $subscriber = new LastChanceNameSubscriber();
        $pattern = $this->prophesize(ComponentInterface::class);
        $pattern->getName()->willReturn('');
        $pattern->getId()->willReturn('foo');
        $pattern->setName('foo')->shouldBeCalled();
        $this->dispatchDiscover($subscriber, $pattern->reveal());
    }

    public function testDoesNotOverrideName()
    {
        $pattern = $this->prophesize(ComponentInterface::class);
        $pattern->getName()->willReturn('foobar');
        $pattern->setName(Argument::type('string'))->shouldNotBeCalled();
        $this->dispatchDiscover(
            new LastChanceNameSubscriber(),
            $pattern->reveal()
        );
    }
}
