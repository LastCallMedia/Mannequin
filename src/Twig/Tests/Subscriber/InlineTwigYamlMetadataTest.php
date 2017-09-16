<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Subscriber;

use LastCall\Mannequin\Core\Tests\Subscriber\DiscoverySubscriberTestTrait;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use PHPUnit\Framework\TestCase;

class InlineTwigYamlMetadataTest extends TestCase
{
    use DiscoverySubscriberTestTrait;
    use YamlParserProphecyTrait;

    private function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'with_info' => '{%block patterninfo %}myinfo{%endblock%}',
            'no_info' => '',
        ]);

        return new \Twig_Environment($loader);
    }

    public function testReadsPatternInfoBlock()
    {
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse('myinfo', 'with_info')->shouldBeCalled();
        $twig = $this->getTwig();
        $source = $twig->getLoader()->getSourceContext('with_info');
        $pattern = new TwigPattern('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $pattern);
    }

    public function testIgnoresPatternsWithNoInfoBlock()
    {
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse()->shouldNotBeCalled();
        $twig = $this->getTwig();
        $source = $twig->getLoader()->getSourceContext('no_info');
        $pattern = new TwigPattern('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $pattern);
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during patterninfo generation of no_exist
     */
    public function testHandlesTwigException()
    {
        $twig = $this->getTwig();
        $parser = $this->prophesize(YamlMetadataParser::class);
        $source = new \Twig_Source('', 'no_exist', '');
        $pattern = new TwigPattern('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $pattern);
    }
}
