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

use LastCall\Mannequin\Core\Tests\Subscriber\ComponentSubscriberTestTrait;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use PHPUnit\Framework\TestCase;

class InlineTwigYamlMetadataSubscriberTest extends TestCase
{
    use ComponentSubscriberTestTrait;
    use YamlParserProphecyTrait;

    private function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'with_info' => '{%block componentinfo %}myinfo{%endblock%}',
            'with_empty_info' => '{%block componentinfo %}{%endblock%}',
            'no_info' => '',
        ]);

        return new \Twig_Environment($loader);
    }

    public function testReadsComponentInfoBlock()
    {
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse('myinfo', 'with_info')->shouldBeCalled();
        $twig = $this->getTwig();
        $source = $twig->getLoader()->getSourceContext('with_info');
        $component = new TwigComponent('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $component);
    }

    public function testIgnoresComponentsWithNoInfoBlock()
    {
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse()->shouldNotBeCalled();
        $twig = $this->getTwig();
        $source = $twig->getLoader()->getSourceContext('no_info');
        $component = new TwigComponent('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $component);
    }

    public function testIgnoresComponentsWithEmptyInfoBlock()
    {
        $parser = $this->prophesize(YamlMetadataParser::class);
        $parser->parse()->shouldNotBeCalled();
        $twig = $this->getTwig();
        $source = $twig->getLoader()->getSourceContext('with_empty_info');
        $component = new TwigComponent('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $component);
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during componentinfo generation of no_exist
     */
    public function testHandlesTwigException()
    {
        $twig = $this->getTwig();
        $parser = $this->prophesize(YamlMetadataParser::class);
        $source = new \Twig_Source('', 'no_exist', '');
        $component = new TwigComponent('', [], $source, $twig);
        $subscriber = new InlineTwigYamlMetadataSubscriber($parser->reveal());
        $this->dispatchDiscover($subscriber, $component);
    }
}
