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
     * Sets the the Twig cache to use for the environment.
     *
     * @param \Twig_CacheInterface $cache
     *
     * @return mixed
     */
    public function setCache(\Twig_CacheInterface $cache);

    /**
     * Return a callable that can be invoked to map an input template name to
     * the a loadable template.
     *
     * This callable may be invoked many times during a single request, so this
     * callback is performance sensitive.
     *
     * @return callable
     */
    public function getTemplateNameMapper(): callable;
}
