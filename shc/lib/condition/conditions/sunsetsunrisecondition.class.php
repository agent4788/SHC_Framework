<?php

namespace SHC\Condition\Conditions;

//Imports
use SHC\Condition\AbstractCondition;
use RWF\Date\DateTime;

/**
 * zwischen Sonnenuntergang und Sonnenaufgang
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SunsetSunriseCondition extends AbstractCondition {

    /**
     * gibt an ob die Bedingung erfuellt ist
     * 
     * @return Boolean
     */
    public function isSatisfies() {

        //wenn deaktiviert immer True
        if (!$this->isEnabled()) {

            return true;
        }

        //Aktuelle Zeit
        $now = DateTime::now();
        $tomorrow = DateTime::nextDay();

        //Datum und Zeit Sonnenaufgang/Sonnenuntergang
        $dateSunriseToday = $now->getSunrise();
        $dateSunsetToday = $now->getSunset();

        //pruefen ob die Zeit zwischen Sonnenuntergang und Sonnenaufgang liegt
        $period = new \DatePeriod($dateSunriseToday, new \DateInterval('PT1M'), $dateSunsetToday);
        $minute = $now->format('H:i');
        foreach ($period as $date) {

            if ($date->format('H:i') == $minute) {

                return false;
            }
        }
        return true;
    }

}
