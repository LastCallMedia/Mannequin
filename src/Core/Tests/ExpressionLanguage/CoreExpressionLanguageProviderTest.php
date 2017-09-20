<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\ExpressionLanguage;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\ComponentRenderer;
use LastCall\Mannequin\Core\ExpressionLanguage\CoreExpressionLanguageProvider;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Stubs\TestComponent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CoreExpressionLanguageProviderTest extends TestCase
{
    public function testMarkup()
    {
        $el = $this->getExpressionLanguage();
        $markup = $el->evaluate('rendered("foo")');
        $this->assertEquals((new Rendered())->setMarkup('foo'), $markup);
    }

    public function testMarkupCompiler()
    {
        $el = $this->getExpressionLanguage();
        $compiled = $el->compile('rendered("foo")');
        $this->assertEquals('(new LastCall\Mannequin\Core\Rendered())->setMarkup("foo")', $compiled);
    }

    public function testAsset()
    {
        $package = new Package(new StaticVersionStrategy('v1'));
        $mannequin = $this->prophesize(Mannequin::class);
        $mannequin->getAssetPackage()->willReturn($package);
        $el = $this->getExpressionLanguage();
        $asset = $el->evaluate('asset("bar")', ['mannequin' => $mannequin->reveal()]);
        $this->assertEquals($package->getUrl('bar'), $asset);
    }

    public function testAssetCompiler()
    {
        $el = $this->getExpressionLanguage();
        $compiled = $el->compile('asset("bar")', ['mannequin']);
        $this->assertEquals('$mannequin->getAssetPackage()->getUrl("bar")', $compiled);
    }

    public function testSample()
    {
        $okRender = (new Rendered())->setMarkup('OK');
        $component = new TestComponent('foo');
        $component->createSample('bar', 'Bar');
        $mannequin = $this->prophesize(Mannequin::class);
        $collection = new ComponentCollection([$component]);

        $renderer = $this->prophesize(ComponentRenderer::class);
        $renderer
            ->render($collection, $component, $component->getSample('bar'))
            ->willReturn($okRender);

        $mannequin->getRenderer()->willReturn($renderer);
        $el = $this->getExpressionLanguage();
        $this->assertEquals($okRender, $el->evaluate('sample("foo#bar")', [
            'mannequin' => $mannequin->reveal(),
            'collection' => $collection,
        ]));
    }

    public function testSampleCompiler()
    {
        $el = $this->getExpressionLanguage();
        $compiled = $el->compile('sample("foo#bar")');
        $this->assertEquals(
            'LastCall\Mannequin\Core\ExpressionLanguage::renderSample($collection, $mannequin->getRenderer(), "foo#bar")',
            $compiled);
    }

    public function getInvalidSampleSpecs()
    {
        return [
            ['foo'],
            ['foo##bar'],
            ['foo#'],
            ['#foo'],
        ];
    }

    /**
     * @dataProvider getInvalidSampleSpecs
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Invalid sample specification: .+/
     */
    public function testSampleWithInvalidSpec($spec)
    {
        $renderer = $this->prophesize(ComponentRenderer::class);
        $mannequin = $this->prophesize(Mannequin::class);
        $mannequin->getRenderer()->willReturn($renderer);
        $el = $this->getExpressionLanguage();
        $el->evaluate(sprintf('sample("%s")', $spec), [
            'mannequin' => $mannequin->reveal(),
            'collection' => new ComponentCollection(),
        ]);
    }

    private function getExpressionLanguage(): ExpressionLanguage
    {
        $el = new ExpressionLanguage();
        $el->registerProvider(new CoreExpressionLanguageProvider());

        return $el;
    }
}
