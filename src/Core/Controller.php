<?php
/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;


use LastCall\Mannequin\Core\Exception\ControllerFrozenException;
use Symfony\Component\Routing\Route;

class Controller
{
    private $route;
    private $routeName;
    private $isFrozen = false;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Gets the controller's route.
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Gets the controller's route name.
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Sets the controller's route.
     *
     * @param string $routeName
     *
     * @return Controller $this The current Controller instance
     */
    public function bind($routeName)
    {
        if ($this->isFrozen) {
            throw new ControllerFrozenException(sprintf('Calling %s on frozen %s instance.', __METHOD__, __CLASS__));
        }

        $this->routeName = $routeName;

        return $this;
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->route, $method)) {
            throw new \BadMethodCallException(sprintf('Method "%s::%s" does not exist.', get_class($this->route), $method));
        }

        call_user_func_array([$this->route, $method], $arguments);

        return $this;
    }

    /**
     * Freezes the controller.
     *
     * Once the controller is frozen, you can no longer change the route name
     */
    public function freeze()
    {
        $this->isFrozen = true;
    }

    public function generateRouteName($prefix)
    {
        $methods = implode('_', $this->route->getMethods()).'_';

        $routeName = $methods.$prefix.$this->route->getPath();
        $routeName = str_replace(['/', ':', '|', '-'], '_', $routeName);
        $routeName = preg_replace('/[^a-z0-9A-Z_.]+/', '', $routeName);

        // Collapse consecutive underscores down into a single underscore.
        $routeName = preg_replace('/_+/', '_', $routeName);

        return $routeName;
    }
}