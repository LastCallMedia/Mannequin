<?php


namespace LastCall\Patterns\Core\Metadata;

use LastCall\Patterns\Core\Pattern\PatternInterface;

interface MetadataFactoryInterface {

  public function hasMetadata(PatternInterface $pattern): bool;

  public function getMetadata(PatternInterface $pattern): array;

}