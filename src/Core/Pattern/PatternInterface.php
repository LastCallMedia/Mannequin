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

    public function addUsedPattern(PatternInterface $pattern): PatternInterface;

    public function getUsedPatterns(): array;

    public function addProblem(string $problem): PatternInterface;

    public function getProblems(): array;
}
