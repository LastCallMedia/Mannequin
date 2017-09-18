<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Component;

use LastCall\Mannequin\Core\Variable\VariableSet;

interface ComponentInterface
{
    /**
     * Get the unique identifier for the component.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get other unique identifiers this component is known by.
     *
     * @return array
     */
    public function getAliases(): array;

    /**
     * Get the human readable name of the component.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the human readable name of the component.
     *
     * @param string $name
     *
     * @return \LastCall\Mannequin\Core\Component\ComponentInterface
     */
    public function setName(string $name): ComponentInterface;

    /**
     * Get all the tags on the component.
     *
     * @return array
     */
    public function getMetadata(): MetadataCollection;

    /**
     * Check whether the component has a given tag.
     *
     * @param $name
     * @param $value
     *
     * @return bool
     */
    public function hasMetadata(string $name, $value): bool;

    /**
     * Add a new tag to the component.
     *
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function addMetadata(string $name, $value): ComponentInterface;

    public function createVariant($id, $name, VariableSet $variables = null, array $metadata = []): Sample;

    /**
     * @return \LastCall\Mannequin\Core\Component\Sample[]
     */
    public function getVariants(): array;

    /**
     * Check whether the component has a named variant.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasVariant(string $name): bool;

    /**
     * Get a variant.
     *
     * @param string $name
     *
     * @return \LastCall\Mannequin\Core\Component\Sample
     */
    public function getVariant(string $name): Sample;

    /**
     * Add a component that this component uses in the course of rendering.
     *
     * @param \LastCall\Mannequin\Core\Component\ComponentInterface $component
     *
     * @return \LastCall\Mannequin\Core\Component\ComponentInterface
     */
    public function addUsedComponent(ComponentInterface $component): ComponentInterface;

    /**
     * Get all of the components that this component "uses".
     *
     * @return array
     */
    public function getUsedComponents(): array;

    /**
     * Note a problem during the discovery process for this component.
     *
     * @param string $problem
     *
     * @return \LastCall\Mannequin\Core\Component\ComponentInterface
     */
    public function addProblem(string $problem): ComponentInterface;

    /**
     * Get an array of all problems with this component.
     *
     * @return array
     */
    public function getProblems(): array;
}
