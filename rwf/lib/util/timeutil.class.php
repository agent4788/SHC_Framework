<?php

namespace RWF\Util;

//Imports
use RWF\Core\RWF;

/**
 * Zeit Funktionen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class TimeUtil {

    /**
     * formatiert eine Sekundenangabe fuer die Anzeige
     *
     * @param  int     $seconds
     * @param  boolean $short
     * @return string
     */
    public static function formatTimefromSeconds($seconds, $short = false) {

        if ($seconds < 0) {
            $seconds = $seconds * -1;
        }

        $jears = 0;
        $month = 0;
        $weeks = 0;
        $days = 0;
        $hours = 0;
        $minutes = 0;

        //Jahre
        $jears_in_sec = 365 * 24 * 60 * 60;
        if ($seconds >= $jears_in_sec) {
            $jears = floor($seconds / $jears_in_sec);
            $seconds -= $jears * $jears_in_sec;
        }
        //Monate
        $month_in_sec = 30 * 24 * 60 * 60;
        if ($seconds >= $month_in_sec) {
            $month = floor($seconds / $month_in_sec);
            $seconds -= $month * $month_in_sec;
        }
        //Wochen
        $weeks_in_sec = 7 * 24 * 60 * 60;
        if ($seconds >= $weeks_in_sec) {
            $weeks = floor($seconds / $weeks_in_sec);
            $seconds -= $weeks * $weeks_in_sec;
        }
        //Tage
        $days_in_sec = 24 * 60 * 60;
        if ($seconds >= $days_in_sec) {
            $days = floor($seconds / $days_in_sec);
            $seconds -= $days * $days_in_sec;
        }
        //Stunden
        if ($seconds >= 3600) {
            $hours = floor($seconds / 3600);
            $seconds -= $hours * 3600;
        }
        //Minuten
        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            $seconds -= $minutes * 60;
        }
        //Sekunden
        $sec = $seconds;

        $first = false;
        $string = '';

        //Jahre
        if ($jears > 0 || $first == true) {
            if ($short == true) {
                $string .= $jears . ' '. RWF::getLanguage()->val('global.date.time.short.jear') .', ';
            } else {
                $string .= $jears . ' ' . ($jears == 1 ? RWF::getLanguage()->val('global.date.time.jear') : RWF::getLanguage()->val('global.date.time.jears')) . ', ';
            }
            $first = true;
        }
        //Monate
        if ($month > 0 || $first == true) {
            if ($short == true) {
                $string .= $month . ' '. RWF::getLanguage()->val('global.date.time.short.month') .', ';
            } else {
                $string .= $month . ' ' . ($month == 1 ? RWF::getLanguage()->val('global.date.time.month') : RWF::getLanguage()->val('global.date.time.months')) . ', ';
            }
            $first = true;
        }
        //Wochen
        if ($weeks > 0 || $first == true) {
            if ($short == true) {
                $string .= $weeks . ' '. RWF::getLanguage()->val('global.date.time.short.week') .', ';
            } else {
                $string .= $weeks . ' ' . ($weeks == 1 ? RWF::getLanguage()->val('global.date.time.week') : RWF::getLanguage()->val('global.date.time.weeks')) . ', ';
            }
            $first = true;
        }
        //Tage
        if ($days > 0 || $first == true) {
            if ($short == true) {
                $string .= $days . ' '. RWF::getLanguage()->val('global.date.time.short.day') .', ';
            } else {
                $string .= $days . ' ' . ($days == 1 ? RWF::getLanguage()->val('global.date.time.day') : RWF::getLanguage()->val('global.date.time.days')) . ', ';
            }
            $first = true;
        }
        //Stunden
        if ($hours > 0 || $first == true) {
            if ($short == true) {
                $string .= $hours . ' '. RWF::getLanguage()->val('global.date.time.short.hour') .', ';
            } else {
                $string .= $hours . ' ' . ($hours == 1 ? RWF::getLanguage()->val('global.date.time.hour') : RWF::getLanguage()->val('global.date.time.hours')) . ', ';
            }
            $first = true;
        }
        //Minuten
        if ($minutes > 0 || $first == true) {
            if ($short == true) {
                $string .= $minutes . ' '. RWF::getLanguage()->val('global.date.time.short.minute') .', ';
            } else {
                $string .= $minutes . ' ' . ($minutes == 1 ? RWF::getLanguage()->val('global.date.time.minute') : RWF::getLanguage()->val('global.date.time.minutes')) . ', ';
            }
        }
        //Sekunden
        if ($short == true) {
            $string .= $sec . ' '. RWF::getLanguage()->val('global.date.time.short.second') .'';
        } else {
            $string .= $sec . ' ' . ($sec == 1 ? RWF::getLanguage()->val('global.date.time.second') : RWF::getLanguage()->val('global.date.time.seconds'));
        }

        return trim($string);
    }

    /**
     * formatiert Millisekunden angaben fuer die ausgabe
     *
     * @param  float $time
     * @return string
     */
    public static function formatMilisecForDisplay($time) {

        if ($time < 1.0) {
            $time *= 1000;
            $time = String::formatFloat($time, 6) . ' ms';
        } else {
            $time = String::formatFloat($time, 6) . ' s';
        }

        return $time;
    }

    /**
     * gibt die Zeitdifferenz in Sekunden zurueck
     *
     * @param  int $mainTimestamp
     * @param  int $compareTimestamp
     * @return int
     */
    public static function timeDiff($mainTimestamp, $compareTimestamp) {

        return $compareTimestamp - $mainTimestamp;
    }

}