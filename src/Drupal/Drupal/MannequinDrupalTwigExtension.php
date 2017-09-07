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

use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Core\Template\TwigExtension;

class MannequinDrupalTwigExtension extends TwigExtension
{
    public function getFilters()
    {
        $filters = parent::getFilters();
        foreach ($filters as $i => $filter) {
            if ($filter instanceof \Twig_SimpleFilter) {
                switch ($filter->getName()) {
                    case 't':
                    case 'trans':
                        // Replace the t filter with our own.
                        $filters[$i] = new \Twig_SimpleFilter($filter->getName(), [$this, 'trans'], ['is_safe' => ['html']]);
                        break;
                }
            }
        }

        return $filters;
    }

    public function trans($string, array $args = [], array $options = [])
    {
        $translation = new TranslationManager(new LanguageDefault([]));

        return new TranslatableMarkup($string, $args, $options, $translation);
    }
}
