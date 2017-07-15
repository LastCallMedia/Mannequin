<?php


namespace LastCall\Mannequin\Core\Tests\Subscriber;


use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\Tests\Stubs\TestFilePattern;
use LastCall\Mannequin\Core\Tests\Stubs\TestPattern;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\YamlMetadataParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class YamlFileMetadataSubscriberTest extends TestCase {

  use DiscoverySubscriberTestTrait;
  use YamlParserProphecyTrait;

  public function setUp() {
    parent::setUp();
    $this->yamlFile = sys_get_temp_dir().'/test.yml';
    $this->templateFile = sys_get_temp_dir().'/test.html';
    (new Filesystem())->touch($this->yamlFile);
  }

  public function testSetsName() {
    $parser = $this->getParserProphecy(['name' => 'foo']);
    $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
    $event = $this->dispatchDiscover(new YamlFileMetadataSubscriber($parser->reveal()), $pattern);
    $this->assertEquals('foo', $event->getPattern()->getName());
  }

  public function testSetsDescription() {
    $parser = $this->getParserProphecy(['description' => 'foo']);
    $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
    $event = $this->dispatchDiscover(new YamlFileMetadataSubscriber($parser->reveal()), $pattern);
    $this->assertEquals('foo', $event->getPattern()->getDescription());
  }

  public function testSetsDefinition() {
    $definition = new Definition(['foo' => 'bar']);
    $parser = $this->getParserProphecy(['definition' => $definition]);
    $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
    $event = $this->dispatchDiscover(new YamlFileMetadataSubscriber($parser->reveal()), $pattern);
    $this->assertSame($definition, $event->getPattern()->getVariableDefinition());
  }

  public function testSetsTags() {
    $parser = $this->getParserProphecy(['tags' => ['foo' => 'bar']]);
    $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
    $event = $this->dispatchDiscover(new YamlFileMetadataSubscriber($parser->reveal()), $pattern);
    $this->assertEquals(['foo' => 'bar'], $event->getPattern()->getTags());
  }

  public function testCanSetDefaultSet() {
    $parser = $this->getParserProphecy(['sets' => ['default' => new Set('Overridden')]]);
    $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
    $event = $this->dispatchDiscover(new YamlFileMetadataSubscriber($parser->reveal()), $pattern);

    $this->assertEquals([
      'default' => new Set('Overridden'),
    ], $event->getPattern()->getVariableSets());
  }

  public function testCanSetAdditionalSet() {
    $parser = $this->getParserProphecy(['sets' => ['additional' => new Set('Additional')]]);
    $pattern = new TestFilePattern('foo', [], new \SplFileInfo($this->templateFile));
    $event = $this->dispatchDiscover(new YamlFileMetadataSubscriber($parser->reveal()), $pattern);

    $this->assertEquals([
      'default' => new Set('Default'),
      'additional' => new Set('Additional')
    ], $event->getPattern()->getVariableSets());
  }
}