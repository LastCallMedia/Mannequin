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

use Alchemy\Zippy\Archive\ArchiveInterface;
use Alchemy\Zippy\Zippy;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use LastCall\Mannequin\Core\Ui\RemoteUi;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class RemoteUiTest extends TestCase
{
    private $cacheDir;
    private static $archives = [
        '0.0.1.tar.gz' => [
            'package/build/index.html' => '',
            'package/build/favicon.ico' => '',
            'package/build/asset-manifest.json' => '{"main.js": "static/js/main.js"}',
        ],
    ];

    private static $packages = [
        'dist-tags' => [
            'latest' => '0.0.1',
        ],
        'versions' => [
            '0.0.1' => [
                'dist' => [
                    'tarball' => 'http://example.com/0.0.1.tar.gz',
                ],
            ],
        ],
    ];

    public function setUp()
    {
        $tmp = tempnam(sys_get_temp_dir(), md5(__CLASS__));
        unlink($tmp);
        mkdir($tmp);
        $this->cacheDir = $tmp;
    }

    public function tearDown()
    {
        (new Filesystem())->remove($this->cacheDir);
    }

    private function mockZippy()
    {
        $self = $this;
        $zippy = $this->prophesize(Zippy::class);
        $zippy->open(Argument::type('string'))->will(function ($args) use ($self) {
            $filename = basename($args[0]);
            if (isset(self::$archives[$filename])) {
                $package = self::$archives[$filename];
                $archive = $self->prophesize(ArchiveInterface::class);
                $archive->extract(Argument::type('string'))->will(function ($args) use ($package) {
                    $fs = new Filesystem();
                    foreach ($package as $relName => $contents) {
                        $fs->dumpFile(sprintf('%s/%s', $args[0], $relName), $contents);
                    }
                });

                return $archive;
            }
        });

        return $zippy->reveal();
    }

    private function mockClient()
    {
        $npmResponse = json_encode(self::$packages);
        $handler = MockHandler::createWithMiddleware([
            new Response(200, [], $npmResponse),
            new Response(200, [], ''),
        ]);

        return new Client(['handler' => $handler]);
    }

    public function testFetchesExplicitVersion()
    {
        $ui = new RemoteUi('0.0.1', $this->mockClient(), $this->mockZippy());
        $ui->setCacheDir($this->cacheDir);
        $manifest = $ui->files();
        foreach (self::$archives['0.0.1.tar.gz'] as $name => $contents) {
            $absPath = sprintf('%s/0.0.1/%s', $this->cacheDir, $name);
            $this->assertStringEqualsFile($absPath, $contents);
            $this->assertContains($absPath, $manifest);
        }
    }

    public function testFetchesDistTaggedVersion()
    {
        $ui = new RemoteUi('latest', $this->mockClient(), $this->mockZippy());
        $ui->setCacheDir($this->cacheDir);
        $manifest = $ui->files();
        foreach (self::$archives['0.0.1.tar.gz'] as $name => $contents) {
            $absPath = sprintf('%s/latest/%s', $this->cacheDir, $name);
            $this->assertStringEqualsFile($absPath, $contents);
            $this->assertContains($absPath, $manifest);
        }
    }

    public function testDoesNotRefetchIfAlreadyFetched()
    {
        $ui = new RemoteUi('latest', $this->mockClient(), $this->mockZippy());
        $ui->setCacheDir($this->cacheDir);
        $ui->files();
        // Dummy assertion to prevent risky test warning.
        // We're really testing for the lack of a Guzzle client
        // exception when the mock queue runs out of responses.
        $this->assertInternalType('array', $ui->files());
    }
}
