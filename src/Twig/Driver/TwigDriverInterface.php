<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Driver;

/**
 * Defines an object that knows how to create the \Twig_Environment.
 *
 * Creating the environment is assumed to be a labor intensive task, so we use
 * this interface to allow the environment to be created only as it's needed.
 * Prefer passing a driver to the full environment whenever possible.
 */
interface TwigDriverInterface
{
    /**
     * Returns the configured \Twig_Environment object.
     *
     * @return \Twig_Environment
     */
    public function getTwig(): \Twig_Environment;

    /**
     * Returns an array of the namespaces known by this driver.
     *
     * The namespaces will be used to map filenames to (namespaced) template
     * names.  It is assumed that these namespaces are already known by the
     * loader.
     *
     * Returned namespaces should be in the form:
     *
     * ```php
     * [
     *   '@somenamespace' => ['templates/somenamespace'],
     *   '@othernamespace' => ['templates/othernamespace']
     * ];
     * ```
     *
     * @return array
     */
    public function getNamespaces(): array;

    /**
     * Returns the root path all Twig templates live under (typically the
     * docroot).
     *
     * @todo: Is this needed, or can it be calculated elsewhere?
     *
     * @return string
     */
    public function getTwigRoot(): string;
}
