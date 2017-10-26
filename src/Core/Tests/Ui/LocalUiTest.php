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
use LastCall\Mannequin\Core\Ui\LocalUi;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

class LocalUiTest extends TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $fs;

    public function setUp()
    {
        $this->fs = vfsStream::setup('root', null, [
            'build' => [
                'index.html' => 'Index',
                'static' => [
                    'css' => [
                        'main.css' => 'CSS',
                    ],
                ],
            ]
        ]);
    }

    public function testIsUiFile()
    {
        $ui = new LocalUi($this->fs->url());
        $this->assertTrue($ui->isUiFile('static/css/main.css'));
    }

    public function testFiles()
    {
        $ui = new LocalUi($this->fs->url());
        $map = [];
        foreach ($ui->files() as $file) {
            $map[$file->getRelativePathname()] = $file->getPathname();
        }
        $this->assertEquals([
            'index.html',
            'static/css/main.css',
        ], array_keys($map));
        foreach ($map as $file) {
            $this->assertFileExists($file);
        }
    }

    public function testGetUiFileResponse()
    {
        $ui = new LocalUi($this->fs->url());
        $this->assertEquals(
            new BinaryFileResponse($this->fs->getChild('build/index.html')->url()),
            $ui->getUiFileResponse('index.html', new Request())
        );
    }

    public function testDecorateRendered()
    {
        $ui = new LocalUi($this->fs->url());
        $rendered = new Rendered(['foo'], ['bar']);
        $rendered->setMarkup('Markup');
        $expected = <<<'EOD'
<html>
<head>
  <script type="text/javascript" src="bar"></script>
  <link rel="stylesheet" href="foo" />
</head>
<body>
  Markup
</body>
EOD;
        $this->assertEquals($expected, $ui->decorateRendered($rendered));
    }
}
