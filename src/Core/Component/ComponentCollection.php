<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Component;

class ComponentCollection implements \Iterator, \Countable
{
    const ROOT_COLLECTION = '__root__';

    private $id = self::ROOT_COLLECTION;

    /**
     * @var \LastCall\Mannequin\Core\Component\ComponentInterface[]
     */
    private $components = [];

    private $aliases = [];

    private $parent;

    /**
     * Constructor.
     *
     * @param array  $components
     * @param string $id
     * @param string $name
     */
    public function __construct(
        array $components = [],
        string $id = self::ROOT_COLLECTION
    ) {
        $this->id = $id;

        foreach ($components as $component) {
            if (!$component instanceof ComponentInterface) {
                throw new \RuntimeException(
                    'Component must be an instance of '.ComponentInterface::class
                );
            }
            $componentId = $component->getId();
            if (isset($this->components[$componentId])) {
                throw new \RuntimeException(
                    sprintf('Duplicate component detected: %s', $componentId)
                );
            }
            $this->components[$componentId] = $component;

            foreach ($component->getAliases() as $alias) {
                if (isset($this->components[$alias])) {
                    throw new \RuntimeException(
                        sprintf(
                            'Alias %s would cause a duplicate component.',
                            $alias
                        )
                    );
                }
                if (isset($this->aliases[$alias])) {
                    throw new \RuntimeException(
                        sprintf(
                            'Alias %s would cause a duplicate component.',
                            $alias
                        )
                    );
                }
                $this->aliases[$alias] = $componentId;
            }
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function rewind()
    {
        return reset($this->components);
    }

    public function valid()
    {
        return null !== key($this->components);
    }

    public function next()
    {
        return next($this->components);
    }

    public function current()
    {
        return current($this->components);
    }

    public function key()
    {
        return key($this->components);
    }

    public function count()
    {
        return count($this->components);
    }

    public function has(string $id)
    {
        if (isset($this->components[$id])) {
            return true;
        }
        if (isset($this->aliases[$id])) {
            return true;
        }

        return false;
    }

    public function get(string $id)
    {
        if (isset($this->components[$id])) {
            return $this->components[$id];
        }
        if (isset($this->aliases[$id])) {
            return $this->get($this->aliases[$id]);
        }
        throw new \RuntimeException(sprintf('Unknown component %s', $id));
    }

    /**
     * @return \LastCall\Mannequin\Core\Component\ComponentCollection|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return \LastCall\Mannequin\Core\Component\ComponentInterface[]
     */
    public function getComponents()
    {
        return array_values($this->components);
    }

    private function setParent(self $parent)
    {
        $this->parent = $parent;
    }

    public function withComponent($id)
    {
        if (isset($this->components[$id])) {
            $subCollection = new static(
                [$this->components[$id]],
                sprintf('component:%s', $id),
                'Component'
            );
            $subCollection->setParent($this);

            return $subCollection;
        }
    }

    public function merge(self $merging)
    {
        $overlapping = array_intersect(
            array_keys($this->components),
            array_keys($merging->components)
        );
        if (count($overlapping)) {
            throw new \RuntimeException(
                sprintf(
                    'Merging these collections would result in the following duplicate components: %s',
                    implode(', ', $overlapping)
                )
            );
        }
        $mergedComponents = array_merge($this->components, $merging->components);
        $merged = new static($mergedComponents, $this->id);
        if ($this->parent) {
            $merged->setParent($this->parent);
        }

        return $merged;
    }
}
