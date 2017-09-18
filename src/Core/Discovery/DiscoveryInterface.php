<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Discovery;

use LastCall\Mannequin\Core\Component\ComponentCollection;

interface DiscoveryInterface
{
    /**
     * Execute discovery of components.
     *
     * All components in the collection MUST have:
     *  - id (suggested default to encoded filename)
     *  - name (suggested default to filename)
     *
     * @throws \LastCall\Mannequin\Core\Exception\UnsupportedComponentException
     * @throws \LastCall\Mannequin\Core\Exception\TemplateParsingException
     *
     * @return \LastCall\Mannequin\Core\Component\ComponentCollection
     */
    public function discover(): ComponentCollection;
}
