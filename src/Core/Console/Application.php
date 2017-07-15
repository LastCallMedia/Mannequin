<?php

namespace LastCall\Mannequin\Core\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends ConsoleApplication
{
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
}
