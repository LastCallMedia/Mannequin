<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Console\Command;

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Ui\FileWriter;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Ui\UiInterface;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class RenderCommand extends Command
{
    private $manifester;

    private $resolver;

    private $engine;

    private $discovery;

    private $ui;

    private $assetFactory;

    private $urlGenerator;

    public function __construct(
        $name = null,
        ManifestBuilder $manifester,
        DiscoveryInterface $discovery,
        UiInterface $ui,
        EngineInterface $engine,
        VariableResolver $resolver,
        AssetFactory $assetFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($name);
        $this->manifester = $manifester;
        $this->discovery = $discovery;
        $this->ui = $ui;
        $this->engine = $engine;
        $this->resolver = $resolver;
        $this->assetFactory = $assetFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function configure()
    {
        $this->setDescription('Render everything to static HTML');
        $this->addOption(
            'output-dir',
            'o',
            InputOption::VALUE_OPTIONAL,
            'The directory to output the UI in',
            'mannequin'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $outDir = $input->getOption('output-dir');

        $writer = new FileWriter($outDir);
        $assetWriter = new AssetWriter($outDir);
        try {
            $collection = $this->discovery->discover();
            $engine = $this->engine;
            $ui = $this->ui;
            $resolver = $this->resolver;

            $manifest = $this->manifester->generate($collection);
            $writer->raw('manifest.json', json_encode($manifest));
            $rows[] = $this->getSuccessRow('Manifest');

            foreach ($manifest['patterns'] as $patternManifest) {
                try {
                    $pattern = $collection->get($patternManifest['id']);
                    $writer->raw(
                        $patternManifest['source'],
                        $engine->renderSource($pattern)
                    );

                    foreach ($patternManifest['variants'] as $variantManifest) {
                        $variant = $pattern->getVariant($variantManifest['id']);
                        $resolved = $resolver->resolve(
                            $variant->getVariables(),
                            [
                                'collection' => $collection,
                                'engine' => $engine,
                                'resolver' => $resolver,
                                'pattern' => $pattern,
                                'variant' => $variant,
                            ]
                        );
                        $context = new RequestContext();
                        $context->setPathInfo('/'.$variantManifest['rendered']);
                        $this->urlGenerator->setContext($context);
                        $rendered = $engine->render($pattern, $resolved);
                        $css = $this->assetFactory->createAsset($rendered->getCss(), [], [
                            'name' => implode('-', ['style', $pattern->getId(), $variant->getId()]),
                            'output' => 'css/*.css',
                        ]);
                        $js = $this->assetFactory->createAsset($rendered->getJs(), [], [
                            'name' => implode('-', ['script', $pattern->getId(), $variant->getId()]),
                            'output' => 'js/*.js',
                        ]);

                        $assetWriter->writeAsset($css);
                        $assetWriter->writeAsset($js);
                        $rendered->setJs([
                            $this->urlGenerator->generate('static', ['name' => $js->getTargetPath()], UrlGeneratorInterface::RELATIVE_PATH),
                        ]);
                        $rendered->setCss([
                            $this->urlGenerator->generate('static', ['name' => $css->getTargetPath()], UrlGeneratorInterface::RELATIVE_PATH),
                        ]);

                        $writer->raw(
                            $variantManifest['source'],
                            $rendered->getMarkup()
                        );
                        $writer->raw(
                            $variantManifest['rendered'],
                            $ui->decorateRendered($rendered)
                        );
                    }
                    $rows[] = $this->getSuccessRow($pattern->getName());
                } catch (\Exception $e) {
                    $rows[] = $this->getErrorRow($pattern->getName(), $e);
                }
            }
            try {
                foreach ($ui->files() as $dest => $src) {
                    $writer->copy($src, $dest);
                }
                $rows[] = $this->getSuccessRow('UI');
            } catch (\Exception $e) {
                $rows[] = $this->getErrorRow('UI', $e);
            }
        } catch (\Exception $e) {
            $rows[] = $this->getErrorRow('Manifest', $e);
        }

        $io->table(['', 'Name', 'Message'], $rows);
    }

    private function getSuccessRow($name)
    {
        return ['<info>✓</info>', $name, ''];
    }

    private function getErrorRow($name, \Exception $e)
    {
        return ['<error>x</error>', $name, $e->getMessage()];
    }
}
