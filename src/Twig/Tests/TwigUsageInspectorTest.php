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

class TwigUsageInspectorTest extends TestCase
{
    public function getTestSets()
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
     * @dataProvider getTestSets
     */
    public function testSet($twigSrc, array $expectedUsed)
    {
        $loader = new \Twig_Loader_Array(
            [
                'mytemplate' => $twigSrc,
                'myparent' => '',
                'myembedded' => '{%block content%}{%endblock%}',
            ]
        );
        $twig = new \Twig_Environment($loader);
        $inspector = new TwigInspector($twig);
        $used = $inspector->inspectLinked(
            new \Twig_Source($twigSrc, 'mytemplate', '')
        );
        $this->assertEquals($expectedUsed, $used);
    }
}
