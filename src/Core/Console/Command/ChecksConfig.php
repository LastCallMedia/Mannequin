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

/**
 * Checks for common problems with the configuration.
 */
trait ChecksConfig
{
    /**
     * Checks a configuration for possible problems, and returns an array of
     * warnings describing the problems.
     *
     * @param \LastCall\Mannequin\Core\Config\ConfigInterface $config
     *
     * @return string[]
     */
    public function checkConfig(ConfigInterface $config): array
    {
        $warnings = [];
        if (1 === count($config->getExtensions())) {
            $warnings[] = 'This configuration does not have any extensions associated with it.  Without extensions, no components will appear in the user interface. See https://mannequin.io/extensions for more information.';
        }

        return $warnings;
    }
}
