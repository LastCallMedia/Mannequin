<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests;

use LastCall\Mannequin\Twig\Driver\PreloadedTwigDriver;
use LastCall\Mannequin\Twig\TwigInspector;
use PHPUnit\Framework\TestCase;

class TwigInspectorTest extends TestCase
{
    private function getDriver()
    {
        $loader = new \Twig_Loader_Array([
            'hasdata' => '{%block patterninfo%}foodata{%endblock%}foocontent',
            'nodata' => 'foocontent',
        ]);

        $twig = new \Twig_Environment($loader);

        return new PreloadedTwigDriver($twig);
    }

    public function testInspectPatternDataReturnsFalseOnNoPatternData()
    {
        $inspector = new TwigInspector($this->getDriver());
        $source = new \Twig_Source('', 'nodata', 'nodata');
        $this->assertFalse($inspector->inspectPatternData($source));
    }

    public function testInspectPatternData()
    {
        $source = new \Twig_Source('', 'hasdata', 'nodata');
        $twig = $this->getDriver();
        $inspector = new TwigInspector($twig);
        $this->assertEquals('foodata', $inspector->inspectPatternData($source));
    }

    public function getInspectLinkedTests()
    {
        return [
            ["{%extends 'myparent' %}", ['myparent']],
            ["{% include 'myparent' %}", ['myparent']],
            [
                "{% embed 'myembedded' %}{%block content %}test{% endblock %}{% endembed %}",
                ['myembedded'],
            ],
            [
                "{%block main%}{%embed 'myembedded'%}{%endembed%}{%endblock%}",
                ['myembedded'],
            ],
            [
                "{%extends 'myparent' %}{%block main%}{%embed 'myembedded'%}{%endembed%}{%endblock%}",
                ['myembedded', 'myparent'],
            ],
        ];
    }

    /**
     * @dataProvider getInspectLinkedTests
     */
    public function testInspectLinked($twigSrc, array $expectedUsed)
    {
        $loader = new \Twig_Loader_Array(
            [
                'mytemplate' => $twigSrc,
                'myparent' => '',
                'myembedded' => '{%block content%}{%endblock%}',
            ]
        );
        $twig = new \Twig_Environment($loader);
        $driver = new PreloadedTwigDriver($twig);
        $inspector = new TwigInspector($driver);
        $used = $inspector->inspectLinked(
            new \Twig_Source($twigSrc, 'mytemplate', '')
        );
        $this->assertEquals($expectedUsed, $used);
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during inspection of nonexistent:
     */
    public function testInspectPatternDataThrowParsingError()
    {
        $source = new \Twig_Source('', 'nonexistent', 'nodata');
        $twig = $this->getDriver();
        $inspector = new TwigInspector($twig);
        $inspector->inspectPatternData($source);
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during inspection of nonexistent:
     */
    public function testInspectLinkedThrowsParsingError()
    {
        $source = new \Twig_Source('{% if foo %}', 'nonexistent', 'nodata');
        $twig = $this->getDriver();
        $inspector = new TwigInspector($twig);
        $inspector->inspectLinked($source);
    }
}
