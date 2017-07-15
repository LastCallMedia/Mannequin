<?php

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;

class Labeller
{
    private $collectionLabels = [
        PatternCollection::ROOT_COLLECTION => 'All Patterns',
    ];

    private $tagLabels = [];

    private $tagWeights = [
        'type' => [
            'atom' => -5,
            'molecule' => -4,
            'organism' => -3,
            'template' => -2,
            'page' => -1,
        ],
    ];

    public function getCollectionLabel(PatternCollection $collection)
    {
        $id = $collection->getId();
        if (isset($this->collectionLabels[$id])) {
            return $this->collectionLabels[$id];
        } elseif (preg_match('/tag:(.*):(.*)/', $id, $matches)) {
            return $this->pluralize(
                $this->getTagLabel($matches[1], $matches[2])
            );
        }

        return $id;
    }

    private function pluralize($word)
    {
        return $word.'s';
    }

    public function getTagLabel($type, $value)
    {
        if (isset($this->tagLabels[$type]) && isset($this->tagLabels[$type][$value])) {
            return $this->tagLabels[$type][$value];
        }

        return ucfirst($value);
    }

    public function getPatternLabel(PatternInterface $pattern)
    {
        return $pattern->getName();
    }

    public function getTagWeight($type, $value)
    {
        if (isset($this->tagWeights[$type]) && isset($this->tagWeights[$type][$value])) {
            return $this->tagWeights[$type][$value];
        }

        return 0;
    }
}
