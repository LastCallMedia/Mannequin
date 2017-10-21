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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\ProcessBuilder;

class StartCommand extends Command
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
        $this->setDescription('Start a web server for live component development');
        $this->addArgument(
            'address',
            InputArgument::OPTIONAL,
            'The address to run on.',
            '*:8000'
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

    private function validateAddress($address)
    {
        if (null === $address) {
            $hostname = '0.0.0.0';
            $port = $this->findBestPort($hostname);
        } elseif (false !== $pos = strrpos($address, ':')) {
            $hostname = substr($address, 0, $pos);
            $port = substr($address, $pos + 1);
        } elseif (ctype_digit($address)) {
            $hostname = '0.0.0.0';
            $port = $address;
        } else {
            $hostname = $address;
            $port = $this->findBestPort($hostname);
        }
        if ('*' === $hostname) {
            $hostname = '0.0.0.0';
        }
        if (!ctype_digit($port)) {
            throw new \InvalidArgumentException(sprintf('Port %s is not valid', $port));
        }

        return sprintf('%s:%s', $hostname, $port);
    }

    private function findBestPort($hostname)
    {
        $port = 8000;
        while (false !== $fp = @fsockopen($hostname, $port, $errno, $errstr, 1)) {
            fclose($fp);
            if ($port++ >= 8100) {
                throw new \RuntimeException('Unable to find a port available to run the web server.');
            }
        }

        return $port;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $address = $this->validateAddress($input->getArgument('address'));

        $routerFile = realpath(__DIR__.'/../../Resources/router.php');
        $builder = $this->getProcessBuilder()
            ->setArguments(['php', '-S', $address, $routerFile])
            ->addEnvironmentVariables([
                'MANNEQUIN_CONFIG' => realpath($this->configFile),
                'MANNEQUIN_AUTOLOAD' => realpath($this->autoloadPath),
                'MANNEQUIN_DEBUG' => $this->debug,
                'MANNEQUIN_VERBOSITY' => $output->getVerbosity(),
            ])
            ->setWorkingDirectory(dirname(realpath($this->configFile)))
            ->setTimeout(null);

        $process = $builder->getProcess();
        $io = new SymfonyStyle($input, $output);
        $message = [sprintf('Starting server on http://%s', $address)];
        if (!$output->isVerbose()) {
            $message[] = 'For debug output, use the -v flag';
        }
        $io->success($message);

        return $this->getHelper('process')->run($output, $process, null, null);
    }
}
