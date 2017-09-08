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

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Stub class to stand in for Drupal's standard DateFormatter.
 */
class MannequinDateFormatter implements DateFormatterInterface
{
    public function format(
        $timestamp,
        $type = 'medium',
        $format = '',
        $timezone = null,
        $langcode = null
    ) {
        switch ($type) {
            case 'custom':
                $format = $format;
                break;
            default:
                $format = 'D, m/d/Y - H:i';
        }
        $timezone = new \DateTimeZone('UTC');

        return DateTimePlus::createFromTimestamp($timestamp, $timezone)->format($format);
    }

    public function formatInterval(
        $interval,
        $granularity = 2,
        $langcode = null
    ) {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function getSampleDateFormats(
        $langcode = null,
        $timestamp = null,
        $timezone = null
    ) {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function formatTimeDiffUntil($timestamp, $options = [])
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function formatTimeDiffSince($timestamp, $options = [])
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }

    public function formatDiff($from, $to, $options = [])
    {
        throw new \Exception(__CLASS__.'::'.__METHOD__.' not yet implemented');
    }
}
