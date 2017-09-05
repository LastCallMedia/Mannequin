<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Twig inspections can be time consuming.  This decorator caches the results
 * so we do not need to recalculate them every time.
 */
class TwigInspectorCacheDecorator implements TwigInspectorInterface
{
    private $decorated;
    private $cache;

    public function __construct(
        TwigInspectorInterface $decorated,
        CacheItemPoolInterface $cache
    ) {
        $this->decorated = $decorated;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function inspectLinked(\Twig_Environment $twig, \Twig_Source $source): array
    {
        $item = $this->cache->getItem($this->getCid($source, 'linked'));
        if (!$item->isHit()) {
            $item->set($this->decorated->inspectLinked($twig, $source));
            $this->cache->save($item);
        }

        return $item->get();
    }

    private function getCid(\Twig_Source $source, string $prefix)
    {
        return $prefix.'.'.md5($source->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function inspectPatternData(\Twig_Environment $twig, \Twig_Source $source)
    {
        $item = $this->cache->getItem($this->getCid($source, 'patterndata'));
        if (!$item->isHit()) {
            $item->set($this->decorated->inspectPatternData($twig, $source));
            $this->cache->save($item);
        }

        return $item->get();
    }
}
