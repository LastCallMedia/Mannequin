<?php

namespace LastCall\Mannequin\Drupal\Extension;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use Symfony\Component\HttpFoundation\Request;

class DrupalBootstrapExtension extends TwigExtension
{
    public function __construct(array $config = [])
    {
        $config += [
            'drupal_root' => null,
            'prefix' => 'drupal',
            'drupal' => function () {
                return $this->bootDrupal();
            },
            'twig_paths' => function () {
                $paths = [];
                $loader = $this['drupal']->get('twig.loader.filesystem');
                foreach ($loader->getNamespaces() as $namespace) {
                    // Skip the main namespace, as it would cause a file scan on the entire
                    // drupal directory.
                    if ($namespace === \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
                        continue;
                    }
                    foreach ($loader->getPaths($namespace) as $path) {
                        if (file_exists($path)) {
                            $paths[$namespace][] = realpath($path);
                        }
                    }
                }

                return $paths;
            },
        ];
        parent::__construct($config);

        $this['_drupal_root'] = function () {
            if (is_dir($this['drupal_root'])) {
                return $this['drupal_root'];
            }
            throw new \InvalidArgumentException(
                sprintf('Invalid Drupal Root: %s', $this['drupal_root'])
            );
        };
        $this['twig'] = function () {
            return $this['drupal']->get('twig');
        };
    }

    protected function bootDrupal()
    {
        $drupal_root = $this['_drupal_root'];
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
