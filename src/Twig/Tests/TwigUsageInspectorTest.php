<?php

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
