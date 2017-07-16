<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Ui\Controller;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManifestController
{
    public function __construct(
        ManifestBuilder $manifester,
        PatternCollection $collection
    ) {
        $this->manifester = $manifester;
        $this->collection = $collection;
    }

    public function getManifestAction()
    {
        return new JsonResponse($this->manifester->generate($this->collection));
    }
}
