<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\DependencyInjection;

use LastCall\Mannequin\Core\Asset\AssetManagerInterface;
use LastCall\Mannequin\Core\Snapshot\CameraInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\ComponentRenderer;
use LastCall\Mannequin\Core\Config\ConfigInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Dependency injection container for Mannequin.
 *
 * @todo: Add logger.
 */
interface ContainerInterface extends PsrContainerInterface
{
    public function getMetadataParser(): YamlMetadataParser;

    public function getVariableResolver(): VariableResolver;

    public function getAssetPackage(): PackageInterface;

    public function getAssetManager(): AssetManagerInterface;

    public function getUrlGenerator(): UrlGeneratorInterface;

    public function getManifestBuilder(): ManifestBuilder;

    public function getDiscovery(): DiscoveryInterface;

    public function getRenderer(): ComponentRenderer;

    public function getCamera(): CameraInterface;

    public function getConfig(): ConfigInterface;

    public function getCache(): CacheItemPoolInterface;

    public function getCacheDir(): string;

    public function isDebug(): bool;
}
