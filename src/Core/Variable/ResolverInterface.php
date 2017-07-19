<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Variable;

interface ResolverInterface
{
    public function resolves(string $type): bool;

    public function resolve(string $type, $value);

    /**
     * Describes all of the types this resolver knows about.
     *
     * Used in debugging the container.
     *
     * @return array
     *               An associative array, with the key being the type, and the value
     *               describing what value you expect to receive, and what it will resolve to
     */
    public function describe(): array;
}
