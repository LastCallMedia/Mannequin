<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Drupal;

use Drupal\Core\Extension\ExtensionDiscovery;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Modifies ExtensionDiscovery to avoid DB-backed function calls, and heavily
 * cache extension data.
 */
class MannequinExtensionDiscovery extends ExtensionDiscovery
{
    public function __construct($root, CacheItemPoolInterface $cache, $profile_directories = null, $site_path = null)
    {
        parent::__construct($root, false, $profile_directories, $site_path);
        $this->cache = $cache;
    }

    public function setProfileDirectoriesFromSettings()
    {
        return $this;
    }

    public function scan($type, $include_tests = null)
    {
        $cid = sprintf('mannequin-drupal-extension-discovery.%s.%s', $type, $include_tests);
        $item = $this->cache->getItem($cid);
        if (!$item->isHit()) {
            $item->set(parent::scan($type, $include_tests));
            $this->cache->save($item);
        }

        return $item->get();
    }
}
