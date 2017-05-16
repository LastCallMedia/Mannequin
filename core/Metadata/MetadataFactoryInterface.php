<?php


namespace LastCall\Mannequin\Core\Metadata;

use LastCall\Mannequin\Core\Pattern\PatternInterface;

interface MetadataFactoryInterface {

  public function hasMetadata(PatternInterface $pattern): bool;

  public function getMetadata(PatternInterface $pattern): array;

}