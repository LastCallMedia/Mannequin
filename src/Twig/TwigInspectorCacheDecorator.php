<?php

namespace LastCall\Mannequin\Twig;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Twig inspections can be time consuming.  This decorator caches the results
 * so we do not need to recalculate them every time.
 */
class TwigInspectorCacheDecorator implements TwigInspectorInterface
{

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
    public function inspectLinked(\Twig_Source $source): array
    {
        $item = $this->cache->getItem($this->getCid($source, 'linked'));
        if ($item->isHit()) {
            return $item->get();
        }
        $linked = $this->decorated->inspectLinked($source);
        $item->set($linked);
        $this->cache->save($item);

        return $linked;
    }

    private function getCid(\Twig_Source $source, string $prefix)
    {
        return $prefix.'.'.md5($source->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function inspectPatternData(\Twig_Source $source)
    {
        $item = $this->cache->getItem($this->getCid($source, 'patterndata'));
        if ($item->isHit()) {
            return $item->get();
        }
        $data = $this->decorated->inspectPatternData($source);
        $item->set($data);
        $this->cache->save($item);

        return $data;
    }

}