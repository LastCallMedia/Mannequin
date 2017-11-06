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

use LastCall\Mannequin\Core\Asset\AssetManagerInterface;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Snapshot\CameraInterface;
use LastCall\Mannequin\Core\Snapshot\DirectorySnapshotWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SnapshotCommand extends Command
{
    private $camera;

    private $discovery;

    private $assetManager;

    protected $writerFactory;

    public function __construct(
        $name = null,
        CameraInterface $camera,
        DiscoveryInterface $discovery,
        AssetManagerInterface $assetManager
    ) {
        parent::__construct($name);
        $this->camera = $camera;
        $this->discovery = $discovery;
        $this->assetManager = $assetManager;
        $this->writerFactory = function ($outputDir) {
            return new DirectorySnapshotWriter($outputDir);
        };
    }

    public function configure()
    {
        $this->setDescription('Render everything to static HTML');
        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_OPTIONAL,
            'The directory to output the UI in',
            'mannequin'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $factory = $this->writerFactory;
        $dir = $input->getOption('output');

        $threwError = false;
        $errorHandler = function (\Exception $e, ComponentInterface $component) use ($io, &$threwError) {
            $threwError = true;
            $io->error(sprintf('Caught exception generating snapshot for %s: %s', $component->getName(), $e->getMessage()));
        };

        $writer = $factory($input->getOption('output'));
        $snapshot = $this->camera->snapshot($this->discovery->discover(), $this->assetManager, $errorHandler);
        $writer->write($snapshot);

        $io->success(sprintf('Wrote snapshot to %s', $dir));

        return (int) $threwError;
    }
}
