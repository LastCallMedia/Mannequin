<?php


namespace LastCall\Mannequin\Twig\Tests\Pattern;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Tests\Pattern\PatternTestCase;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;

class TwigPatternTest extends PatternTestCase {

  public function getPattern(): PatternInterface {
    $src = new \Twig_Source('', 'test', self::TEMPLATE_FILE);
    return new TwigPattern(self::PATTERN_ID, self::PATTERN_ALIASES, $src);
  }
}