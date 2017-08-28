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

    private function getTwig($templates)
    {
        return new \Twig_Environment(new \Twig_Loader_Array($templates));
    }

    public function testInspectPatternDataReturnsFalseOnNoPatternData()
    {
        $twig = $this->getTwig([
            'nodata' => 'foocontent',
        ]);
        $inspector = new TwigInspector();
        $data = $inspector->inspectPatternData(
            $twig, $twig->load('nodata')->getSourceContext()
        );
        $this->assertFalse($data);
    }

    public function testInspectPatternData()
    {
        $twig = $this->getTwig([
            'hasdata' => '{%block patterninfo%}foodata{%endblock%}foocontent',
        ]);
        $inspector = new TwigInspector();
        $data = $inspector->inspectPatternData($twig, $twig->load('hasdata')->getSourceContext());
        $this->assertEquals('foodata', $data);
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
        $twig = $this->getTwig([
            'mytemplate' => $twigSrc,
            'myparent' => '',
            'myembedded' => '{%block content%}{%endblock%}',
        ]);
        $inspector = new TwigInspector();
        $used = $inspector->inspectLinked(
            $twig,
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
        $loader = new \Twig_Loader_Array([]);
        $twig = new \Twig_Environment($loader);
        $inspector = new TwigInspector();
        $inspector->inspectPatternData($twig, $source);
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during inspection of nonexistent:
     */
    public function testInspectLinkedThrowsParsingError()
    {
        $source = new \Twig_Source('{% if foo %}', 'nonexistent', 'nodata');
        $twig = new \Twig_Environment(new \Twig_Loader_Array([]));
        $inspector = new TwigInspector($twig);
        $inspector->inspectLinked($twig, $source);
    }
}
