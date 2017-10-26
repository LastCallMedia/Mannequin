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

use LastCall\Mannequin\Core\Config\ConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\ProcessBuilder;

class StartCommand extends Command
{
    use ChecksConfig;

    private $debug;

    private $config;

    private $configFile;

    private $autoloadFile;

    private $processBuilder;

    public function __construct(
        $name,
        ConfigInterface $config,
        string $configFile,
        string $autoloadFile,
        bool $debug = false
    ) {
        parent::__construct($name);
        $this->config = $config;
        $this->configFile = $configFile;
        $this->autoloadFile = $autoloadFile;
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

    private function getPort($address)
    {
        $pos = strrpos($address, ':');

        return substr($address, $pos + 1);
    }

    private function getHost($address)
    {
        $pos = strrpos($address, ':');

        return substr($address, 0, $pos);
    }

    /**
     * Checks if we are running from inside a Docker container.
     *
     * @return bool
     */
    private function isInsideDocker()
    {
        return file_exists('/.dockerenv');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $address = $this->validateAddress($input->getArgument('address'));
        $warnings = $this->checkConfig($this->config, $io);
        if ($warnings) {
            $io->warning(array_merge(['There were possible problems found with your configuration:'], $warnings));
        }

        $routerFile = realpath(__DIR__.'/../../Resources/router.php');
        $builder = $this->getProcessBuilder()
            ->setArguments(['php', '-S', $address, $routerFile])
            ->addEnvironmentVariables([
                'MANNEQUIN_CONFIG' => $this->configFile,
                'MANNEQUIN_AUTOLOAD' => $this->autoloadFile,
                'MANNEQUIN_DEBUG' => $this->debug,
                'MANNEQUIN_VERBOSITY' => $output->getVerbosity(),
            ])
            ->setWorkingDirectory($this->config->getDocroot())
            ->setTimeout(null);

        $process = $builder->getProcess();

        $io->title('Starting Mannequin development server...');
        $io->text('Tips:');
        $tips = [
            sprintf('Visit http://%s in your web browser.', $address),
            'Log messages will be printed below. Use the -v flag for more log data.',
        ];
        if ($this->isInsideDocker()) {
            $tips[] = sprintf('Ensure that port %d is exposed, and %s is reachable from your host machine.', $this->getPort($address), $this->getHost($address));
        }
        $io->listing($tips);

        return $process->run(function ($type, $buffer) use ($process, $output) {
            if ($process::ERR === $type && $output instanceof ConsoleOutputInterface) {
                $output->getErrorOutput()->write($buffer);
            } else {
                $output->write($buffer);
            }
        });
    }
}
