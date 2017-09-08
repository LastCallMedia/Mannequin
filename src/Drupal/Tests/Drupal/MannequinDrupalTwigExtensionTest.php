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
            ["{{ ['foo', 'bar']| safe_join(', ') }}", 'foo, bar'],
            ["{{ {foo: 'bar', baz: 'bar'} | without('baz') | json_encode }}", htmlentities(json_encode(['foo' => 'bar']))],
            ["{{ 'foo\"' | clean_class }}", 'foo'],
            ["{{ 'foo\"' | clean_id }}", 'foo'],
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
            ["{{path('foo')}}", '#'],
        ];
    }

    /**
     * @dataProvider getFunctionTests
     */
    public function testFunction($input, $expectedOutput)
    {
        $this->assertRenderedEquals($input, $expectedOutput);
    }

    public function testPath()
    {
        $this->assertRenderedEquals(
            "{{path('foo')}}",
            '#'
        );
    }

    public function testUrl()
    {
        $this->assertRenderedEquals(
            "{{url('foo')}}",
            '#'
        );
    }

    public function testFileUrl()
    {
        $this->assertRenderedEquals(
            "{{file_url('foo')}}",
            'foo'
        );
    }

    public function testAttachLibrary()
    {
        $this->assertRenderedEquals(
            "{{attach_library('system/foo')}}",
            ''
        );
    }

    public function testActiveThemePath()
    {
        $this->assertRenderedEquals(
            '{{active_theme_path}}',
            ''
        );
    }

    public function testActiveTheme()
    {
        $this->assertRenderedEquals(
            '{{active_theme}}',
            ''
        );
    }

    public function testCreateAttribute()
    {
        $this->assertRenderedEquals(
            "{{create_attribute({foo: 'bar'})}}",
            ' foo="bar"'
        );
    }

    public function testLink()
    {
        $this->markTestSkipped('Rendering not yet implemented');
        $this->assertRenderedEquals(
            "{{link('Foo', 'base:/#')}}",
            'base:/#'
        );
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
