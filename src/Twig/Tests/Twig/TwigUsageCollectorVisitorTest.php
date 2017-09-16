<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Twig;

use LastCall\Mannequin\Twig\Twig\TwigUsageCollectorVisitor;
use PHPUnit\Framework\TestCase;
use Twig\Loader\ArrayLoader;

class TwigUsageCollectorVisitorTest extends TestCase
{
    private function getTwig()
    {
        $loader = new ArrayLoader([
            'included' => '{%block content%}foo{% endblock %}',
            'include' => "{%include 'included'%}",
            'embed' => "{%embed 'included'%}{%endembed%}",
            'extends' => "{%extends 'included'%}",
            'include_in_block' => "{%block foo %}{%include 'included'%}{%endblock%}",
            'embed_in_block' => "{%block foo %}{%embed 'included'%}{%endembed%}{%endblock%}",
        ]);

        $twig = new \Twig_Environment($loader);
        $twig->addNodeVisitor(new TwigUsageCollectorVisitor());

        return $twig;
    }

    public function testDoesNotMessUpBody()
    {
        $twig = $this->getTwig();
        $this->assertEquals('foo', $twig->render('included'));
    }

    public function testWritesIncludesBlockEvenWhenEmpty()
    {
        $twig = $this->getTwig();
        $template = $twig->load('included');
        $this->assertTrue($template->hasBlock('_collected_usage'));
    }

    public function testWritesIncludesBlock()
    {
        $twig = $this->getTwig();
        $template = $twig->load('include');
        $this->assertTrue($template->hasBlock('_collected_usage'));
    }

    public function getInclusionTests()
    {
        return [
            ['include', ['included']],
            ['embed', ['included']],
            ['extends', ['included']],
            ['include_in_block', ['included']],
            ['embed_in_block', ['included']],
        ];
    }

    /**
     * @dataProvider getInclusionTests
     */
    public function testIncludesBlock($template, $expectedIncludes)
    {
        $twig = $this->getTwig();
        $template = $twig->load($template);
        $block = $template->renderBlock('_collected_usage');
        $this->assertEquals($expectedIncludes, json_decode($block));
    }
}
