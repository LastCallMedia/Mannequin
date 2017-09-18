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

use LastCall\Mannequin\Core\Exception\VariantNotFoundException;
use LastCall\Mannequin\Core\Variable\VariableSet;

abstract class AbstractComponent implements ComponentInterface
{
    protected $id;

    protected $aliases = [];

    private $name = '';

    private $tags = [];

    private $variants = [];

    private $used = [];

    private $problems = [];

    public function __construct($id, array $aliases = [])
    {
        $this->id = $id;
        $this->aliases = $aliases;
        $this->tags = new MetadataCollection(static::getDefaultMetadata());
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
    public function setName(string $name): ComponentInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): MetadataCollection
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadata(string $name, $value): bool
    {
        return isset($this->tags[$name]) && $this->tags[$name] === $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addMetadata(string $name, $value): ComponentInterface
    {
        $this->tags[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createVariant($id, $name, VariableSet $variables = null, array $tags = []): Sample
    {
        return $this->variants[$id] = new Sample($id, $name, $variables, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariant(string $name): bool
    {
        return isset($this->variants[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant(string $name): Sample
    {
        if (!isset($this->variants[$name])) {
            throw new VariantNotFoundException(sprintf('Variant %s not found', $name));
        }

        return $this->variants[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function addUsedComponent(ComponentInterface $component): ComponentInterface
    {
        $this->used[] = $component;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedComponents(): array
    {
        return $this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function addProblem(string $problem): ComponentInterface
    {
        $this->problems[] = $problem;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProblems(): array
    {
        return $this->problems;
    }

    protected static function getDefaultMetadata(): array
    {
        return [
            'group' => 'Unknown',
            'source_format' => 'html',
        ];
    }
}