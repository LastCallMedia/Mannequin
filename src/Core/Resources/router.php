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
use LastCall\Mannequin\Core\Config\ConfigLoader;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Logger\ConsoleLogger;

if (getenv('MANNEQUIN_AUTOLOAD')) {
    require_once getenv('MANNEQUIN_AUTOLOAD');
}

// Cue PHP to serve static files if they exist, as long as they don't match
// one of our protected patterns.  As much as we'd love to control each request,
// this can slow down the development server by an order of magnitude.
if (is_file($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$_SERVER['SCRIPT_NAME'])) {
    if (!preg_match('@^/(index.php$|index.html$|favicon.ico$|static/|m-)@', $_SERVER['SCRIPT_NAME'])) {
        return false;
    }
}

// Override SCRIPT_FILENAME, which can come in as the URL requested, if the URL
// matches an existing file.
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

$output = new ConsoleOutput(getenv('MANNEQUIN_VERBOSITY'));

$config = ConfigLoader::loadReaddressable(getenv('MANNEQUIN_CONFIG'), getenv('MANNEQUIN_AUTOLOAD'));
$app = new Mannequin($config, [
    'debug' => getenv('MANNEQUIN_DEBUG') ?? false,
    'logger' => new ConsoleLogger($output),
]);

$app->run();
