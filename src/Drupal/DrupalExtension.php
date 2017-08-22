<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use Drupal\Core\Template\Attribute;
use LastCall\Mannequin\Twig\AbstractTwigExtension;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides Drupal Twig template discovery and rendering.
 */
class DrupalExtension extends AbstractTwigExtension implements ExpressionFunctionProviderInterface
{
    private $drupal;
    private $iterator;
    private $drupalRoot;

    public function __construct(array $config = [])
    {
        $this->iterator = $config['finder'] ?: new \ArrayIterator([]);
        $this->drupalRoot = $config['drupal_root'] ?? getcwd();
        if (!is_dir($this->drupalRoot) || !file_exists($this->drupalRoot.'/autoload.php')) {
            throw new \InvalidArgumentException(
                sprintf('Unable to detect Drupal installation in %s', $this->drupalRoot)
            );
        }
    }

    public function getFunctions()
    {
        $attributes = new ExpressionFunction('attributes', function ($args) {
            throw new \InvalidArgumentException('Attributes cannot be compiled.');
        }, function ($args, $attrs) {
            return new Attribute($attrs);
        });

        return [$attributes];
    }

    protected function getIterator()
    {
        return $this->iterator;
    }

    protected function getTwig(): \Twig_Environment
    {
        return $this->getDrupal()->get('twig');
    }

    protected function getLoader(): \Twig_LoaderInterface
    {
        return $this->getDrupal()->get('twig.loader.filesystem');
    }

    protected function getGlobs(): array
    {
        return $this->globs;
    }

    protected function getTwigRoot(): string
    {
        return $this->drupalRoot;
    }

    protected function getNamespaces(): array
    {
        $namespaces = [];
        $loader = $this->getLoader();
        if ($loader instanceof \Twig_Loader_Filesystem) {
            foreach ($loader->getNamespaces() as $namespace) {
                $namespaces[$namespace] = $loader->getPaths($namespace);
            }
        }

        return $namespaces;
    }

    private function getDrupal()
    {
        if (!$this->drupal) {
            $this->drupal = $this->bootDrupal();
        }

        return $this->drupal;
    }

    private function bootDrupal()
    {
        $drupal_root = $this->drupalRoot;
        chdir($drupal_root);
        $autoloader = require $drupal_root.'/autoload.php';
        require_once $drupal_root.'/core/includes/bootstrap.inc';

        $request = Request::create(
            '/',
            'GET',
            [],
            [],
            [],
            ['SCRIPT_NAME' => $drupal_root.'/index.php']
        );
        $kernel = DrupalKernel::createFromRequest(
            $request,
            $autoloader,
            'prod',
            false
        );
        Settings::initialize(
            $drupal_root,
            DrupalKernel::findSitePath($request),
            $autoloader
        );
        $kernel->boot();
        $kernel->preHandle($request);

        return $kernel->getContainer();
    }
}
