<?php

namespace LastCall\Mannequin\Html\Tests\Pattern;

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Tests\Pattern\PatternTestCase;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;

class HtmlPatternTest extends PatternTestCase
{

    public function getPattern(): PatternInterface
    {
        return new HtmlPattern(
            self::PATTERN_ID,
            self::PATTERN_ALIASES,
            new \SplFileInfo(self::TEMPLATE_FILE)
        );
    }
}