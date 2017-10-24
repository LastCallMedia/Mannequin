<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Ui;

use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Ui\CheckingUiDecorator;
use LastCall\Mannequin\Core\Ui\UiInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CheckingUiDecoratorTest extends TestCase
{
    private $fooUrl;

    public function setUp()
    {
        vfsStream::setup('root');
        mkdir(vfsStream::url('root/existing'));
        $this->fooUrl = vfsStream::url('root/existing');
    }

    public function testFilesSuccessfulCheck()
    {
        $inner = $this->prophesize(UiInterface::class);
        $inner->files()->shouldBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', $this->fooUrl);
        $decorator->files();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFilesErrorCheck()
    {
        $inner = $this->prophesize(UiInterface::class);
        $inner->files()->shouldNotBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', '');
        $decorator->files();
    }

    public function testIsUiFileSuccessfulCheck()
    {
        $filename = 'foobar';
        $inner = $this->prophesize(UiInterface::class);
        $inner->isUiFile($filename)->shouldBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', $this->fooUrl);
        $decorator->isUiFile($filename);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsUiFileErrorCheck()
    {
        $filename = 'foobar';
        $inner = $this->prophesize(UiInterface::class);
        $inner->isUiFile($filename)->shouldNotBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', '');
        $decorator->isUiFile($filename);
    }

    public function testGetIndexFileResponseSuccessfulCheck()
    {
        $request = new Request();
        $inner = $this->prophesize(UiInterface::class);
        $inner->getIndexFileResponse($request)->shouldBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', $this->fooUrl);
        $decorator->getIndexFileResponse($request);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetIndexFileResponseErrorCheck()
    {
        $request = new Request();
        $inner = $this->prophesize(UiInterface::class);
        $inner->getIndexFileResponse($request)->shouldNotBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', '');
        $decorator->getIndexFileResponse($request);
    }

    public function testGetUiFileResponseSuccessfulCheck()
    {
        $request = new Request();
        $inner = $this->prophesize(UiInterface::class);
        $inner->getUiFileResponse('', $request)->shouldBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', $this->fooUrl);
        $decorator->getUiFileResponse('', $request);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetUiFileResponseErrorCheck()
    {
        $request = new Request();
        $inner = $this->prophesize(UiInterface::class);
        $inner->getUiFileResponse('', $request)->shouldNotBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', '');
        $decorator->getUiFileResponse('', $request);
    }

    public function testDecorateRenderedSuccessfulCheck()
    {
        $rendered = new Rendered();
        $inner = $this->prophesize(UiInterface::class);
        $inner->decorateRendered($rendered)->shouldBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', $this->fooUrl);
        $decorator->decorateRendered($rendered);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDecorateRenderedErrorCheck()
    {
        $rendered = new Rendered();
        $inner = $this->prophesize(UiInterface::class);
        $inner->decorateRendered($rendered)->shouldNotBeCalled();
        $decorator = new CheckingUiDecorator($inner->reveal(), '', '');
        $decorator->decorateRendered($rendered);
    }
}
