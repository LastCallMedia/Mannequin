<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Config;

use LastCall\Mannequin\Core\ConfigInterface;

class ConfigLoader
{
    public static function load(string $filename): ConfigInterface
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException(sprintf('Expected config in %s, but the file does not exist.', $filename), 1);
        }
        try {
            $config = require $filename;
        } catch (Exception $e) {
            throw new \RuntimeException(sprintf('There was an error loading config from %s. Message: %s', $filename, $e->getMessage()), 1, $e);
        }
        if (1 === $config) {
            throw new \RuntimeException(sprintf('No configuration was returned from %s.', $filename), 1);
        }
        if (!$config instanceof ConfigInterface) {
            throw new \RuntimeException(sprintf('Configuration returned from %s is not an instance of %s.', $filename, ConfigInterface::class), 1);
        }

        return $config;
    }
}
