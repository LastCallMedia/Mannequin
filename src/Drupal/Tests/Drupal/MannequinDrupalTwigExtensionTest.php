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
            ["{{'translated' | trans }}", 'translated'],
            ["{{'translated' | t }}", 'translated'],
            ["{{'<foo' | placeholder}}", '<em class="placeholder">&lt;foo</em>'],
            ["{{'<foo' | drupal_escape}}", '&lt;foo'],
            ["{{ ['foo', 'bar']| safe_join(', ') }}", 'foo, bar'],
            ["{{ {foo: 'bar', baz: 'bar'} | without('baz') | json_encode }}", htmlentities(json_encode(['foo' => 'bar']))],
            ["{{ 'foo\"' | clean_class }}", 'foo'],
            ["{{ 'foo\"' | clean_id }}", 'foo'],
            ['{{1 | render}}', '1'],
            ["{{'foo' | render}}", 'foo'],
            // @todo: Test coverage for render obj
            ['{{ 999999999 | format_date }}', 'Sun, 09/09/2001 - 01:46'],
        ];
    }

    /**
     * @dataProvider getFilterTests
     */
    public function testFilter($input, $expectedOutput)
    {
        $this->assertRenderedEquals($input, $expectedOutput);
    }

    public function getFunctionTests()
    {
        return [
            ['{{render_var(1)}}', '1'],
            ['{{render_var("foo")}}', 'foo'],
            // @todo: Test coverage for render obj
            ["{{url('foo')}}", '#'],
            ["{{path('foo')}}", '#'],
            ["{{link('Foo', 'base:/#')}}", '<a href="#">Foo</a>'],
            ["{{link('Foo', 'base:/#', {class: ['foo']})}}", '<a class="foo" href="#">Foo</a>'],
            ["{{file_url('foo')}}", 'foo'],
            ["{{attach_library('system/foo')}}", ''],
            ['{{active_theme_path}}', ''],
            ['{{active_theme}}', ''],
            ["{{create_attribute({foo: 'bar'})}}", ' foo="bar"'],
        ];
    }

    /**
     * @dataProvider getFunctionTests
     */
    public function testFunction($input, $expectedOutput)
    {
        $this->assertRenderedEquals($input, $expectedOutput);
    }

    public function assertRenderedEquals($template, $expected)
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
        $this->assertEquals($expected, $twig->render('test'));
    }
}
