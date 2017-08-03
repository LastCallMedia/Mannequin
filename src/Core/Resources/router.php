<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use LastCall\Mannequin\Core\Mannequin;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Logger\ConsoleLogger;

if (getenv('MANNEQUIN_AUTOLOAD')) {
    require_once getenv('MANNEQUIN_AUTOLOAD');
}

$output = new ConsoleOutput(getenv('MANNEQUIN_VERBOSITY'));
$app = new Mannequin([
    'debug' => getenv('MANNEQUIN_DEBUG') ?? false,
    'autoload_file' => getenv('MANNEQUIN_AUTOLOAD'),
    'config_file' => getenv('MANNEQUIN_CONFIG'),
    'logger' => new ConsoleLogger($output),
]);

$app->run();
