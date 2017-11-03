<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Console\Helper;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Stores the path to autoload.php so it can be retrieved later on.
 *
 * This class is a temporary shim until we can implement a more robust
 * server solution that doesn't require reloading the config in a separate
 * process.
 *
 * @see \LastCall\Mannequin\Core\Console\Command\StartCommand
 */
class StartHelper implements HelperInterface
{
    private $set;
    private $autoloadFile = '';
    private $configFile = '';

    public function __construct(string $autoloadFile = '', string $configFile = '')
    {
        $this->autoloadFile = $autoloadFile;
        $this->configFile = $configFile;
    }

    public function getName()
    {
        return 'start';
    }

    public function setHelperSet(HelperSet $helperSet = null)
    {
        $this->set = $helperSet;
    }

    public function getHelperSet()
    {
        return $this->set;
    }

    public function setConfigFile(string $file)
    {
        $this->configFile = $file;
    }

    public function getConfigFile(): string
    {
        return $this->configFile;
    }

    public function getAutoloadFile(): string
    {
        return $this->autoloadFile;
    }
}
