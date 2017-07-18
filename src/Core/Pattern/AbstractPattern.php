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

use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;

abstract class AbstractPattern implements PatternInterface
{
    protected $id;

    protected $aliases = [];

    private $name = '';

    private $description = '';

    private $tags = [];

    private $variableDefinition;

    private $variableSets = [];

    private $used = [];

    public function __construct($id, array $aliases = [])
    {
        $this->id = $id;
        $this->aliases = $aliases;
        $this->variableSets['default'] = new Set('Default', []);
        $this->tags = self::getDefaultTags();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): PatternInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description): PatternInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag(string $name, $value): bool
    {
        return isset($this->tags[$name]) && $this->tags[$name] === $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(string $name, $value): PatternInterface
    {
        $this->tags[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableDefinition(): Definition
    {
        return $this->variableDefinition ?: new Definition([]);
    }

    /**
     * {@inheritdoc}
     */
    public function setVariableDefinition(Definition $definition
    ): PatternInterface {
        $this->variableDefinition = $definition;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addVariableSet(string $id, Set $set): PatternInterface
    {
        $this->variableSets[$id] = $set;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableSets(): array
    {
        return $this->variableSets;
    }

    public function addUsedPattern(PatternInterface $pattern): PatternInterface
    {
        $this->used[] = $pattern;

        return $this;
    }

    public function getUsedPatterns(): array
    {
        return $this->used;
    }

    protected static function getDefaultTags(): array
    {
        return [
            'category' => 'Unknown',
            'source_format' => 'html',
        ];
    }
}
