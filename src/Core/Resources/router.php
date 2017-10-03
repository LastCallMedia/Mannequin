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

// @todo: This improves performance of serving assets by letting PHP resolve the
// asset path directly, but it comes at the cost of possibly having our internal
// paths hijacked by local files.  Evaluate post 1.0.0 whether it's worth it.
if (is_file($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$_SERVER['SCRIPT_NAME'])) {
    return false;
}

// Override SCRIPT_FILENAME, which can come in as the URL requested, if the URL
// matches an existing file.
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

$output = new ConsoleOutput(getenv('MANNEQUIN_VERBOSITY'));
$app = new Mannequin([
    'debug' => getenv('MANNEQUIN_DEBUG') ?? false,
    'autoload_file' => getenv('MANNEQUIN_AUTOLOAD'),
    'config_file' => getenv('MANNEQUIN_CONFIG'),
    'logger' => new ConsoleLogger($output),
]);

$app->run();
