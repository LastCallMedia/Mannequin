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

use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class DebugCommand extends Command
{
    private $builder;
    private $discovery;

    public function __construct($name, ManifestBuilder $builder, DiscoveryInterface $discovery)
    {
        parent::__construct($name);
        $this->builder = $builder;
        $this->discovery = $discovery;
    }

    public function configure()
    {
        $this->setDescription('Display information on patterns and variable types');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->block('Patterns');
        $manifest = $this->builder->generate($this->discovery->discover());
        $yaml = Yaml::dump($manifest['patterns'], 5);
        $output->write($yaml);
    }
}
