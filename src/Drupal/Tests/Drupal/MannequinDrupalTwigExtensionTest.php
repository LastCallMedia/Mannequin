<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests\Drupal;

use Drupal\Core\Render\Markup;
use LastCall\Mannequin\Drupal\Drupal\MannequinDateFormatter;
use LastCall\Mannequin\Drupal\Drupal\MannequinDrupalTwigExtension;
use LastCall\Mannequin\Drupal\Drupal\MannequinRenderer;
use LastCall\Mannequin\Drupal\Drupal\MannequinThemeManager;
use LastCall\Mannequin\Drupal\Drupal\MannequinUrlGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for Mannequin/Drupal twig functionality.
 */
class MannequinDrupalTwigExtensionTest extends TestCase
{
    public function getFilterTests()
    {
        return [
            ["{{'translated' | trans }}", 'translated', 'Trans should pass the string through.'],
            ["{{'<translated' | trans }}", '<translated', 'Trans should escape output.'],
            ["{{'translated' | t }}", 'translated', 't should pass the string through.'],
            ["{{'<foo' | placeholder}}", '<em class="placeholder">&lt;foo</em>', 'placeholder should create placeholder markup.'],
            ["{{'<foo' | drupal_escape}}", '&lt;foo', 'drupal_escape should escape markup.'],
            ["{{ ['foo', 'bar']| safe_join(', ') }}", 'foo, bar', 'safe_join should join strings.'],
            ["{{ {foo: 'bar', baz: 'bar'} | without('baz') | json_encode }}", htmlentities(json_encode(['foo' => 'bar'])), 'without should remove elements based on their keys.'],
            ["{{ 'foo\"' | clean_class }}", 'foo', 'clean_class should escape and remove characters.'],
            ["{{ 'foo\"' | clean_id }}", 'foo', 'clean_id should remove disallowed characters'],
            ['{{1 | render}}', '1', 'render should pass integers through.'],
            ["{{'foo' | render}}", 'foo', 'render should pass strings through.'],
            ['{{["foo", "bar"] | render}}', 'foobar', 'render should join arrays.'],
            // @todo: Test coverage for render obj
            ['{{ 999999999 | format_date }}', 'Sun, 09/09/2001 - 01:46', 'format_date should format a timestamp.'],
        ];
    }

    /**
     * @dataProvider getFilterTests
     */
    public function testFilter($input, $expectedOutput, $message)
    {
        $this->assertRenderedEquals($input, $expectedOutput, $message);
    }

    public function getFunctionTests()
    {
        return [
            ['{{render_var(1)}}', '1', 'Integers should be passed through render.'],
            ['{{render_var("foo")}}', 'foo', 'Simple strings should be passed through render.'],
            ['{{render_var(["foo", "bar"])}}', 'foobar', 'Rendered arrays should be concatenated.'],
            // @todo: Test coverage for render obj
            ["{{url('foo')}}", '#', 'Urls should always be generated as a simple anchor.'],
            ["{{path('foo')}}", '#', 'Paths should always be generated as a simple anchor.'],
            ["{{link('Foo', 'base:/#')}}", '<a href="#">Foo</a>', 'Links should be output.'],
            ["{{link('Foo', 'base:/#', {class: ['foo']})}}", '<a class="foo" href="#">Foo</a>', 'Links should allow attributes.'],
            ["{{file_url('foo')}}", 'foo', 'File Urls should be passed through directly.'],
            ["{{attach_library('system/foo')}}", '', 'Attach library should result in no output.'],
            ['{{active_theme_path}}', '', 'Active theme path should result in no output.'],
            ['{{active_theme}}', '', 'active_theme should result in no output.'],
            ["{{create_attribute({foo: 'bar'})}}", ' foo="bar"', 'create_attribute should output an attribute string.'],
        ];
    }

    /**
     * @dataProvider getFunctionTests
     */
    public function testFunction($input, $expectedOutput, $message)
    {
        $this->assertRenderedEquals($input, $expectedOutput, $message);
    }

    /**
     * Tests functionality that delegates out to the renderer.
     */
    public function getRenderTests()
    {
        return [
            ["{{['foo', 'bar']}}", 'foobar', 'Arrays of strings should be concatenated.'],
            ["{{[['foo'], ['bar']]}}", 'foobar', 'Nested arrays of strings should be concatenated'],
            ["{{['<foo>']}}", '&lt;foo&gt;', 'Unescaped strings should be escaped'],
            ['{{[stm]}}', '<i>stm</i>', '\Twig_Markup objects should not be escaped.'],
            ['{{[sdm]}}', '<i>sdm</i>', 'MarkupInterface objects should not be escaped.'],
            ["{{[stm, '<foo>']}}", '<i>stm</i>&lt;foo&gt;', 'Safe strings and nonsafe strings can be combined and are escaped properly.'],
        ];
    }

    /**
     * @dataProvider getRenderTests
     */
    public function testRender($input, $expected, $message)
    {
        $this->assertRenderedEquals($input, $expected, $message);
    }

    public function getBlockTests()
    {
        return [
          ['{% trans %}translated{% endtrans %}', 'translated', 'Translated blocks should pass through.'],
          ['{% trans %}Singular{%plural one %}test{% endtrans %}', 'Singular', 'Translated plural blocks with a singular count should show the singular version.'],
          ['{% trans %}Singular {%plural two %}Plural{% endtrans %}', 'Plural', 'Translated plural blocks with a multiple count should show the plural version.'],
        ];
    }

    /**
     * @dataProvider getBlockTests
     */
    public function testBlock($input, $expectedOutput, $message)
    {
        $this->assertRenderedEquals($input, $expectedOutput, $message);
    }

    public function assertRenderedEquals($template, $expected, $message = '')
    {
        $loader = new \Twig_Loader_Array(['test' => $template]);
        $twig = new \Twig_Environment($loader);
        $extension = new MannequinDrupalTwigExtension(
            new MannequinRenderer(),
            new MannequinUrlGenerator(),
            new MannequinThemeManager(),
            new MannequinDateFormatter()
        );
        $twig->addExtension($extension);
        $this->assertEquals($expected, $twig->render('test', [
            'stm' => new \Twig_Markup('<i>stm</i>', 'UTF-8'),
            'sdm' => Markup::create('<i>sdm</i>'),
            'one' => 1,
            'two' => 2,
        ]), $message);
    }
}
