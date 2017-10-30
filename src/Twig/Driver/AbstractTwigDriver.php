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

use LastCall\Mannequin\Twig\TemplateNameMapper;
use LastCall\Mannequin\Twig\Twig\Lexer;
use LastCall\Mannequin\Twig\Twig\MannequinExtension;

/**
 * Base class Twig Drivers can extend to do less work.
 */
abstract class AbstractTwigDriver implements TwigDriverInterface
{
    private $initialized;
    protected $twig;
    protected $cache;

    /**
     * {@inheritdoc}
     */
    public function getTwig(): \Twig_Environment
    {
        if (!$this->twig) {
            $this->twig = $this->createTwig();
        }
        if (!$this->initialized) {
            $this->initialize($this->twig);
            $this->finalize($this->twig);
            $this->initialized = true;
        }

        return $this->twig;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(\Twig_CacheInterface $cache)
    {
        if ($this->initialized) {
            throw new \RuntimeException('Cannot call setCache on a TwigDriver that has already been initialized.');
        }
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateNameMapper(): callable
    {
        $mapper = new TemplateNameMapper($this->getTwigRoot());
        foreach ($this->getNamespaces() as $namespace => $paths) {
            $mapper->addNamespace($namespace, $paths);
        }

        return $mapper;
    }

    /**
     * @param \Twig_Environment $twig
     */
    protected function initialize(\Twig_Environment $twig)
    {
        $twig->addExtension(new MannequinExtension());
        if ($this->cache) {
            $twig->setCache($this->cache);
        }
        $twig->enableAutoReload();
    }

    /**
     * Finish modifying the Twig Environment.
     *
     * Do anything here that needs to happen right before the environment is
     * initialized, but after any extensions/settings have been added.
     *
     * @param \Twig_Environment $twig
     */
    protected function finalize(\Twig_Environment $twig)
    {
        $twig->setLexer(new Lexer($twig));
    }

    /**
     * Return the path below which all Twig templates can be found.
     *
     * @return string
     */
    abstract protected function getTwigRoot(): string;

    /**
     * Return a list of the namespaces this driver understands.
     *
     * The namespaces will be used to map filenames to (namespaced) template
     * names.  It is assumed that these namespaces are already known by the
     * loader.
     *
     * Returned namespaces should be in the form:
     * ```php
     * [
     *   '@somenamespace' => ['templates/somenamespace'],
     *   '@othernamespace' => ['templates/othernamespace']
     * ];
     * ```
     *
     * @return array
     */
    abstract protected function getNamespaces(): array;

    /**
     * Do whatever work is necessary to create the Twig environment.
     *
     * @return \Twig_Environment
     */
    abstract protected function createTwig(): \Twig_Environment;
}
