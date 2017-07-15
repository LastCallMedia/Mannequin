<?php


namespace LastCall\Mannequin\Core\Event;


use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use Symfony\Component\EventDispatcher\Event;

class PatternDiscoveryEvent extends Event
{

    public function __construct(
        PatternInterface $pattern,
        PatternCollection $collection
    ) {
        $this->pattern = $pattern;
        $this->collection = $collection;
    }

    public function getPattern(): PatternInterface
    {
        return $this->pattern;
    }

    public function getCollection(): PatternCollection
    {
        return $this->collection;
    }

}