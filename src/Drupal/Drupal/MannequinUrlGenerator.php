<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Drupal;

use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Stub class to stand in for Drupal's standard URL Generator.
 */
class MannequinUrlGenerator implements UrlGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        throw new \Exception('Method not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        throw new \Exception('Method not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getPathFromRoute($name, $parameters = [])
    {
        throw new \Exception('Method not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromRoute($name, $parameters = [], $options = [], $collect_bubbleable_metadata = false)
    {
        return '#';
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        return '#';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        throw new \Exception('Method not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDebugMessage($name, array $parameters = [])
    {
        throw new \Exception('Method not yet implemented');
    }
}
