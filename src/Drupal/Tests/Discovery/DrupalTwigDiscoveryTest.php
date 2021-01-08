<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests\Discovery;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Drupal\Component\DrupalTwigComponent;
use LastCall\Mannequin\Drupal\Discovery\DrupalTwigDiscovery;
use PHPUnit\Framework\TestCase;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;

class DrupalTwigDiscoveryTest extends TestCase
{
    use IdEncoder;

    private function getTwig()
    {
        $loader = new \Twig\Loader\ArrayLoader([
            'form-input.twig' => 'I am twig code',
            'broken' => '{% }}',
        ]);

        return new \Twig\Environment($loader, [
            'cache' => false,
            'auto_reload' => true,
        ]);
    }

    private function getDriver(\Twig\Environment $twigEnvironment)
    {
        $driver = $this->prophesize(TwigDriverInterface::class);
        $driver->getTwig()->willReturn($twigEnvironment);

        return $driver->reveal();
    }

    public function testDiscoversCollection()
    {
        $driver = $this->getDriver($this->getTwig());
        $discovery = new DrupalTwigDiscovery($driver, ['form-input.twig']);
        $collection = $discovery->discover();
        $this->assertInstanceOf(ComponentCollection::class, $collection);
        $this->assertCount(1, $collection);

        return $collection;
    }

    /**
     * @depends testDiscoversCollection
     */
    public function testDiscoversComponent(ComponentCollection $collection)
    {
        $component = $collection->get(
            $this->encodeId('form-input.twig')
        );
        $this->assertInstanceOf(DrupalTwigComponent::class, $component);

        return $component;
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsId(DrupalTwigComponent $component)
    {
        $this->assertEquals(
            $this->encodeId('form-input.twig'),
            $component->getId()
        );
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsName(DrupalTwigComponent $component)
    {
        $this->assertEquals('form-input.twig', $component->getName());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsAliases(DrupalTwigComponent $component)
    {
        $this->assertEquals(['form-input.twig'], $component->getAliases());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsFilename(DrupalTwigComponent $component)
    {
        $this->assertFalse($component->getFile());
    }

    /**
     * @depends testDiscoversComponent
     */
    public function testSetsSource(DrupalTwigComponent $component)
    {
        $source = $component->getSource();
        $this->assertInstanceOf(\Twig\Source::class, $source);
        $this->assertEquals('form-input.twig', $source->getName());
    }
}
