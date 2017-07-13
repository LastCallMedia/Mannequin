<?php


namespace LastCall\Mannequin\Core\Ui\Controller;


use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\Manifester;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManifestController {

  public function __construct(Manifester $manifester, PatternCollection $collection) {
    $this->manifester = $manifester;
    $this->collection = $collection;
  }

  public function getManifestAction() {
    return new JsonResponse($this->manifester->generate($this->collection));
  }
}