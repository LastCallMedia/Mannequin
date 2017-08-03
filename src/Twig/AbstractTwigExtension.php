<?php
/**
 * Created by PhpStorm.
 * User: rbayliss
 * Date: 8/3/17
 * Time: 7:10 AM
 */

namespace LastCall\Mannequin\Twig;


use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Twig\Discovery\TwigDiscovery;
use LastCall\Mannequin\Twig\Engine\TwigEngine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;

abstract class AbstractTwigExtension extends AbstractExtension
{

    public function getDiscoverers(): array
    {
        return [
            new TwigDiscovery(
                $this->getTwig()->getLoader(), $this->getIterator()
            )
        ];
    }

    public function getEngines(): array
    {
        $config = $this->mannequin->getConfig();
        $styles = $config->getStyles();
        $scripts = $config->getScripts();
        return [
            new TwigEngine($this->getTwig(), $styles, $scripts)
        ];
    }

    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $inspector = $this->getInspector();
        $dispatcher->addSubscriber(
            new InlineTwigYamlMetadataSubscriber($inspector)
        );
        $dispatcher->addSubscriber(
            new TwigIncludeSubscriber($inspector)
        );
    }

    protected function getIterator() {
        return new TwigLoaderIterator(
            $this->getLoader(),
            $this->getTwigRoot(),
            $this->getGlobs()
        );
    }

    protected function getInspector() {
        // @todo: Wrap with caching inspector.
        return new TwigInspector($this->getTwig());
    }

    abstract protected function getTwig(): \Twig_Environment;
    abstract protected function getLoader(): \Twig_LoaderInterface;
    abstract protected function getTwigRoot(): string;
    abstract protected function getGlobs(): array;

}