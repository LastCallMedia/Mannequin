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

use LastCall\Mannequin\Twig\TwigInspector;
use PHPUnit\Framework\TestCase;

class TwigInspectorTest extends TestCase
{
    private function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'hasdata' => '{%block patterninfo%}foodata{%endblock%}foocontent',
            'nodata' => 'foocontent',
        ]);

        return new \Twig_Environment($loader);
    }

    public function testInspectPatternDataReturnsFalseOnNoPatternData()
    {
        $inspector = new TwigInspector($this->getTwig());
        $source = new \Twig_Source('', 'nodata', 'nodata');
        $this->assertFalse($inspector->inspectPatternData($source));
    }

    public function testInspectPatternData()
    {
        $source = new \Twig_Source('', 'hasdata', 'nodata');
        $twig = $this->getTwig();
        $inspector = new TwigInspector($twig);
        $this->assertEquals('foodata', $inspector->inspectPatternData($source));
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during inspection of nonexistent:
     */
    public function testInspectPatternDataThrowParsingError()
    {
        $source = new \Twig_Source('', 'nonexistent', 'nodata');
        $twig = $this->getTwig();
        $inspector = new TwigInspector($twig);
        $inspector->inspectPatternData($source);
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during inspection of nonexistent:
     */
    public function testInspectLinkedThrowParsingError()
    {
        $source = new \Twig_Source('{% if foo %}', 'nonexistent', 'nodata');
        $twig = $this->getTwig();
        $inspector = new TwigInspector($twig);
        $inspector->inspectLinked($source);
    }
}
