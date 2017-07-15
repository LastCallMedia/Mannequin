<?php

namespace LastCall\Mannequin\Core\Tests\Subscriber;

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
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
        $pattern = $this->prophesize(TemplateFilePatternInterface::class);
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
        $pattern = $this->prophesize(PatternInterface::class);
        $pattern->getName()->willReturn('');
        $pattern->getId()->willReturn('foo');
        $pattern->setName('foo')->shouldBeCalled();
        $this->dispatchDiscover($subscriber, $pattern->reveal());
    }

    public function testDoesNotOverrideName()
    {
        $pattern = $this->prophesize(PatternInterface::class);
        $pattern->getName()->willReturn('foobar');
        $pattern->setName(Argument::type('string'))->shouldNotBeCalled();
        $this->dispatchDiscover(
            new LastChanceNameSubscriber(),
            $pattern->reveal()
        );
    }
}
