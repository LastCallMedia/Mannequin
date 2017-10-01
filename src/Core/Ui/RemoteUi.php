<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui;

use Alchemy\Zippy\Zippy;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use LastCall\Mannequin\Core\Common\DirectoryCachingInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Remote fetching UI.
 *
 * This class fetches the UI files from NPM if they haven't been downloaded
 * already.
 */
class RemoteUi extends LocalUi implements DirectoryCachingInterface
{
    private $fetched = null;
    private $cacheDir;
    private $uiVersion;
    private $client;
    private $filesystem;

    public function __construct($uiVersion = 'latest', ClientInterface $client = null, Zippy $zippy = null)
    {
        $this->uiVersion = $uiVersion;
        $this->filesystem = new Filesystem();
        $this->client = $client ?? new Client();
        $this->zippy = $zippy ?? Zippy::load();
    }

    public function isUiFile(string $path): bool
    {
        $this->checkFetched();

        return parent::isUiFile($path);
    }

    public function files(): \Traversable
    {
        $this->checkFetched();

        return parent::files();
    }

    public function setCacheDir(string $dir)
    {
        $this->cacheDir = $dir;
    }

    protected function uiPath($relativePath = '')
    {
        if (!$this->cacheDir) {
            throw new \RuntimeException('Cache directory has not been set.');
        }

        return rtrim(sprintf('%s/package/%s', $this->versionDir(), $relativePath), '/');
    }

    private function versionDir()
    {
        return sprintf('%s/%s', $this->cacheDir, $this->uiVersion);
    }

    private function checkFetched()
    {
        if (null === $this->fetched) {
            $this->fetched = file_exists($this->uiPath('package.json'));
            if (!$this->fetched) {
                $url = $this->getFetchUrl();
                if ($tmpFile = $this->fetch($url)) {
                    $this->fetched = $this->extract($tmpFile, $this->versionDir());
                }
            }
            if (!$this->fetched) {
                throw new \RuntimeException(sprintf('Unable to download UI package to %s', $this->downloadDir));
            }
        }
    }

    private function getFetchUrl()
    {
        if ($response = $this->client->get('https://registry.npmjs.org/lastcall-mannequin-ui')) {
            $contents = \GuzzleHttp\json_decode($response->getBody(), true);
            $version = 'invalid';
            if (isset($contents['versions'][$this->uiVersion])) {
                $version = $this->uiVersion;
            } elseif (isset($contents['dist-tags'][$this->uiVersion])) {
                $version = $contents['dist-tags'][$this->uiVersion];
            }
            if ($version === 'invalid') {
                throw new \RuntimeException(sprintf('Unable to find requested UI version: %s', $this->uiVersion));
            }

            return $contents['versions'][$version]['dist']['tarball'];
        }
        throw new \RuntimeException('Invalid response from NPM server.');
    }

    private function fetch($url)
    {
        $this->filesystem->mkdir($this->cacheDir);
        $zipFile = sprintf('%s/%s', $this->cacheDir, basename($url));
        if (!file_exists($zipFile)) {
            $this->client->get($url, ['save_to' => $zipFile]);
        }

        return $zipFile;
    }

    private function extract($tmpFile, $destination)
    {
        $this->filesystem->mkdir($destination);
        $archive = $this->zippy->open($tmpFile);
        $archive->extract($destination);

        return true;
    }
}
