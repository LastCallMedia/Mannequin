<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Drupal;

use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Stub class to stand in for Drupal's standard ThemeManager.
 */
class MannequinThemeManager implements ThemeManagerInterface
{
    public function render($hook, array $variables)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function getActiveTheme()
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function hasActiveTheme()
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function resetActiveTheme()
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function setActiveTheme(ActiveTheme $active_theme)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function alter($type, &$data, &$context1 = null, &$context2 = null)
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function alterForTheme(
        ActiveTheme $theme,
        $type,
        &$data,
        &$context1 = null,
        &$context2 = null
    ) {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }
}
