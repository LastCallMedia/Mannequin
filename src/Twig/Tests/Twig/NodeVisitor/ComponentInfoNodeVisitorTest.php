<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Twig\NodeVisitor;

use LastCall\Mannequin\Twig\Twig\Lexer;
use LastCall\Mannequin\Twig\Twig\NodeVisitor\ComponentInfoNodeVisitor;
use LastCall\Mannequin\Twig\Twig\TokenParser\CommentTokenParser;
use PHPUnit\Framework\TestCase;
use Twig\Loader\ArrayLoader;

class ComponentInfoNodeVisitorTest extends TestCase
{
    private static $templates = [
        'info' => '{# @Component foo#}bar',
        'no_info' => 'bar',
        'extended' => '{# @Component extended#}{%block content%}foo{%endblock%}',
        'extending' => "{# @Component extending#}{% extends 'extended' %}{%block content%}Extending{%endblock%}",
        'extending_no' => "{% extends 'extended' %}{%block content%}Extending{%endblock%}",
    ];

    private function getTwig(array $templates = null)
    {
        if (!$templates) {
            $templates = self::$templates;
        }
        $loader = new ArrayLoader($templates);
        $twig = new \Twig_Environment($loader);
        $twig->addNodeVisitor(new ComponentInfoNodeVisitor());
        $twig->addTokenParser(new CommentTokenParser());
        $twig->setLexer(new Lexer($twig));

        return $twig;
    }

    public function getParseInfoTests()
    {
        return [
            ["{#@component\nfoo#}", 'foo'],
            ["{#@Component\nfoo#}", 'foo'],
            ["{# @Component\nfoo#}", 'foo'],
            ['{#@Componentfoo#}', ''],
        ];
    }

    /**
     * @dataProvider getParseInfoTests
     */
    public function testParseInfo($input, $expected)
    {
        $twig = $this->getTwig(['test' => $input]);
        $this->assertEquals($expected, $twig->load('test')->renderBlock('componentinfo'));
    }

    public function testDoesNotMessUpBody()
    {
        $twig = $this->getTwig();
        $template = $twig->load('info');
        $this->assertEquals('bar', $template->render());
    }

    public function testHasInfoBlockWhenComponentContent()
    {
        $twig = $this->getTwig();
        $template = $twig->load('info');
        $this->assertTrue($template->hasBlock('componentinfo'));
    }

    public function testHasInfoBlockWhenNoComponentContent()
    {
        $twig = $this->getTwig();
        $template = $twig->load('no_info');
        $this->assertTrue($template->hasBlock('componentinfo'));
    }

    public function testExtendingOverridesExtended()
    {
        $twig = $this->getTwig();
        $template = $twig->load('extending');
        $this->assertEquals('extending', $template->renderBlock('componentinfo'));
    }

    public function testExtendingWithoutInfoOverridesExtended()
    {
        $twig = $this->getTwig();
        $template = $twig->load('no_info');
        $this->assertEquals('', $template->renderBlock('componentinfo'));
    }
}
