<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Pattern;

use LastCall\Mannequin\Core\Variable\VariableSet;

interface PatternInterface
{
    /**
     * Get the unique identifier for the pattern.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get other unique identifiers this pattern is known by.
     *
     * @return array
     */
    public function getAliases(): array;

    /**
     * Get the human readable name of the pattern.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the human readable name of the pattern.
     *
     * @param string $name
     *
     * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
     */
    public function setName(string $name): PatternInterface;

    /**
     * Get all the tags on the pattern.
     *
     * @return array
     */
    public function getTags(): array;

    /**
     * Check whether the pattern has a given tag.
     *
     * @param $name
     * @param $value
     *
     * @return bool
     */
    public function hasTag(string $name, $value): bool;

    /**
     * Add a new tag to the pattern.
     *
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function addTag(string $name, $value): PatternInterface;

    public function createVariant($id, $name, VariableSet $variables = null, array $tags = []): PatternVariant;

    /**
     * @return \LastCall\Mannequin\Core\Pattern\PatternVariant[]
     */
    public function getVariants(): array;

    /**
     * Check whether the pattern has a named variant.
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
     * @return \LastCall\Mannequin\Core\Pattern\PatternVariant
     */
    public function getVariant(string $name): PatternVariant;

    /**
     * Add a pattern that this pattern uses in the course of rendering.
     *
     * @param \LastCall\Mannequin\Core\Pattern\PatternInterface $pattern
     *
     * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
     */
    public function addUsedPattern(PatternInterface $pattern): PatternInterface;

    /**
     * Get all of the patterns that this pattern "uses".
     *
     * @return array
     */
    public function getUsedPatterns(): array;

    /**
     * Note a problem during the discovery process for this pattern.
     *
     * @param string $problem
     *
     * @return \LastCall\Mannequin\Core\Pattern\PatternInterface
     */
    public function addProblem(string $problem): PatternInterface;

    /**
     * Get an array of all problems with this pattern.
     *
     * @return array
     */
    public function getProblems(): array;
}
