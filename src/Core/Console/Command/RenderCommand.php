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

use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\PatternRenderer;
use LastCall\Mannequin\Core\Ui\FileWriter;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Ui\UiInterface;
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

    private $discovery;

    private $ui;

    private $urlGenerator;

    public function __construct(
        $name = null,
        ManifestBuilder $manifester,
        DiscoveryInterface $discovery,
        UiInterface $ui,
        UrlGeneratorInterface $urlGenerator,
        PatternRenderer $renderer
    ) {
        parent::__construct($name);
        $this->manifester = $manifester;
        $this->discovery = $discovery;
        $this->ui = $ui;
        $this->urlGenerator = $urlGenerator;
        $this->renderer = $renderer;
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
        try {
            $collection = $this->discovery->discover();
            $ui = $this->ui;
            $renderer = $this->renderer;
            $urlGenerator = $this->urlGenerator;

            $manifest = $this->manifester->generate($collection);
            $writer->raw('manifest.json', json_encode($manifest));
            $rows[] = $this->getSuccessRow('Manifest');

            foreach ($collection as $pattern) {
                try {
                    $writer->raw(
                        $this->urlGenerator->generate('pattern_render_source_raw', ['pattern' => $pattern->getId()]),
                        $renderer->renderSource($pattern)
                    );
                    foreach ($pattern->getVariants() as $variant) {
                        $args = ['pattern' => $pattern->getId(), 'variant' => $variant->getId()];
                        $ctx = new RequestContext();
                        $ctx->setPathInfo($urlGenerator->generate('pattern_render', $args));
                        $urlGenerator->setContext($ctx);
                        $rendered = $renderer->render($collection, $pattern, $variant);
                        $writer->raw(
                            $urlGenerator->generate('pattern_render', $args),
                            $this->ui->decorateRendered($rendered)
                        );
                        $writer->raw(
                            $urlGenerator->generate('pattern_render_raw', $args),
                            $rendered->getMarkup()
                        );
                        $renderer->writeAssets($rendered, $outDir);
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
