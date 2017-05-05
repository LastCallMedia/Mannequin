<?php


namespace LastCall\Patterns\Core\Tests\Discovery;


use LastCall\Patterns\Core\Discovery\TemplateDiscovery;
use LastCall\Patterns\Core\Parser\TemplateFileParserInterface;
use LastCall\Patterns\Core\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;

class TemplateDiscoveryTest extends TestCase {

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage  Template file parsers must implement LastCall\Patterns\Core\Parser\TemplateFileParserInterface
   */
  public function testInvalidParsers() {
    new TemplateDiscovery(new Finder(), [new \stdClass()]);
  }

  public function testCallsSupports() {
    $finder = new Finder();
    $finder->in([__DIR__.'/../Resources']);

    $parser = $this->prophesize(TemplateFileParserInterface::class);
    $parser
      ->supports(Argument::type(\SplFileInfo::class))
      ->shouldBeCalled();
    $discovery = new TemplateDiscovery($finder, [$parser->reveal()]);
    $discovery->discover();
  }

  public function testCallsParse() {
    $finder = new Finder();
    $finder->in([__DIR__.'/../Resources']);

    $pattern = new HtmlPattern('foo', 'Bar', 'baz');

    $parser = $this->prophesize(TemplateFileParserInterface::class);
    $parser->supports(Argument::any())->willReturn(TRUE);
    $parser->parse(Argument::type(\SplFileInfo::class))
      ->shouldBeCalled()
      ->willReturn($pattern);

    $discovery = new TemplateDiscovery($finder, [$parser->reveal()]);
    $collection = $discovery->discover();
    $this->assertEquals([$pattern], $collection->getPatterns());
  }
}