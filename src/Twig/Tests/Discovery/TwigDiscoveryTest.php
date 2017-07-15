<?php

namespace LastCall\Mannequin\Twig\Tests\Discovery;

use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;

class TwigDiscoveryTest extends TestCase
{
    use IdEncoder;

    const FIXTURES_DIR = __DIR__.'/../Resources';

    public function testSetsId()
    {
        $pattern = $this->discoverFixtureCollection()->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertInstanceOf(TwigPattern::class, $pattern);
        $this->assertEquals(
            $this->encodeId('form-input.twig'),
            $pattern->getId()
        );
    }

    private function discoverFixtureCollection()
    {
        $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
        $discoverer = new TwigDiscovery($loader, [['form-input.twig']]);

        return $discoverer->discover();
    }

    public function testSetsAliases()
    {
        $pattern = $this->discoverFixtureCollection()->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertEquals(['form-input.twig'], $pattern->getAliases());
    }

    public function testSetsSource()
    {
        /** @var TwigPattern $pattern */
        $pattern = $this->discoverFixtureCollection()->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertInstanceOf(\Twig_Source::class, $pattern->getSource());
        $source = $pattern->getSource();
        $this->assertEquals('form-input.twig', $source->getName());
        $this->assertEquals(
            realpath(self::FIXTURES_DIR.'/form-input.twig'),
            $source->getPath()
        );
        $this->assertContains('<input', $source->getCode());
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     * @expectedExceptionMessage Unable to load some-nonexistent-file.twig
     */
    public function testThrowsExceptionOnNonLoadableFiles()
    {
        $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
        $discoverer = new TwigDiscovery(
            $loader,
            [['some-nonexistent-file.twig']]
        );
        $discoverer->discover();
    }
}
