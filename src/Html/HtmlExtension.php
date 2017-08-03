<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html;

use LastCall\Mannequin\Core\Extension\AbstractExtension;
use LastCall\Mannequin\Core\Iterator\MappingCallbackIterator;
use LastCall\Mannequin\Core\Iterator\RelativePathMapper;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Engine\HtmlEngine;

class HtmlExtension extends AbstractExtension
{
    private $finder;

    public function __construct(array $values = [])
    {
        if(isset($values['finder'])) {
            $this->finder = $values['finder'];
        }
    }

    public function getDiscoverers(): array
    {
        return [
            new HtmlDiscovery($this->getIterator())
        ];
    }

    public function getEngines(): array
    {
        $config = $this->getConfig();
        return [
            new HtmlEngine($config->getStyles(), $config->getScripts())
        ];
    }

    private function getIterator() {
        return new MappingCallbackIterator(
            $this->finder,
            new RelativePathMapper()
        );
    }
}
