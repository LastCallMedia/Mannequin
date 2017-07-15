<?php


namespace LastCall\Mannequin\Core\Discovery;


use LastCall\Mannequin\Core\Pattern\PatternCollection;

class ExplicitDiscovery implements DiscoveryInterface
{

    private $patternCollection;

    public function __construct(PatternCollection $collection)
    {
        $this->patternCollection = $collection;
    }

    public function discover(): PatternCollection
    {
        return $this->patternCollection;
    }
}