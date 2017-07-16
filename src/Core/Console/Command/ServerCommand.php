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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class ServerCommand extends Command
{
    private $autoloadPath;

    private $configFile;

    private $debug;

    private $processBuilder;

    public function __construct(
        $name,
        string $configFile,
        string $autoloadPath,
        bool $debug = false
    ) {
        parent::__construct($name);
        $this->autoloadPath = $autoloadPath;
        $this->configFile = $configFile;
        $this->debug = $debug;
    }

    public function configure()
    {
        $this->addArgument(
            'address',
            InputArgument::OPTIONAL,
            'The address to run on.',
            '127.0.0.1:8000'
        );
        $this->addOption(
            'output-dir',
            'o',
            InputOption::VALUE_OPTIONAL,
            'The directory to output the UI in'
        );
    }

    private function getProcessBuilder(): ProcessBuilder
    {
        return $this->processBuilder ?? new ProcessBuilder();
    }

    public function setProcessBuilder(ProcessBuilder $builder)
    {
        $this->processBuilder = $builder;

        return $this;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $address = $input->getArgument('address');

        $routerFile = realpath(__DIR__.'/../../Resources/router.php');
        $builder = $this->getProcessBuilder()
            ->setArguments(['php', '-S', $address, $routerFile])
            ->addEnvironmentVariables([
                'MANNEQUIN_CONFIG' => realpath($this->configFile),
                'MANNEQUIN_AUTOLOAD' => realpath($this->autoloadPath),
                'MANNEQUIN_DEBUG' => $this->debug,
            ])
            ->setWorkingDirectory(realpath(__DIR__.'/../../Resources'))
            ->setTimeout(null);

        return $builder->getProcess()
            ->setTty(true)
            ->run();
    }
}
