<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Console;

use LastCall\Mannequin\Core\Config\ConfigLoader;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Version;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    private $autoloadPath;

    public function __construct(string $autoloadPath)
    {
        $this->autoloadPath = $autoloadPath;
        parent::__construct('Mannequin', Version::id());
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Path to the configuration file',
                '.mannequin.php'
            )
        );
        $definition->addOption(
            new InputOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Enable debug mode'
            )
        );

        return $definition;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getParameterOption(['--config', '-c'], '.mannequin.php');
        $debug = $input->getParameterOption(['--debug', '-d'], false);

        $config = ConfigLoader::load($configFile);

        $mannequin = new Mannequin($config, [
            'debug' => $debug,
            'logger' => new ConsoleLogger($output),
            'config_file' => $configFile,
            'autoload_file' => $this->autoloadPath,
        ]);
        $this->setDispatcher($mannequin['dispatcher']);
        $mannequin->boot();
        $this->addCommands($mannequin['commands']);
        parent::doRun($input, $output);
    }
}
