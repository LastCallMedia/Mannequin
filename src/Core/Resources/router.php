<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (getenv('MANNEQUIN_AUTOLOAD')) {
    require_once getenv('MANNEQUIN_AUTOLOAD');
}

$app = new \LastCall\Mannequin\Core\Application([
    'debug' => getenv('MANNEQUIN_DEBUG') ?? false,
    'autoload_file' => getenv('MANNEQUIN_AUTOLOAD'),
    'config_file' => getenv('MANNEQUIN_CONFIG'),
]);

$app->run();
