<?php

namespace SHC\Condition\Conditions;

//Imports
use RWF\Date\Calendar\Holidays\GermanHolidays;
use SHC\Condition\AbstractCondition;
use RWF\Date\DateTime;

/**
 * Bedingung Feiertage
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HolidaysCondition extends AbstractCondition {

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

        //noetige Parameter pruefen
        if (!isset($this->data['holidays'])) {

            throw new Exception('Die Feiertage müssen angegeben werden', 1580);
        }

        $holidays = $this->data['holidays'];
        $now = DateTime::now();
        $dateNow = $now->format('dmY');
        $germanHolidays = new GermanHolidays($now->getYear());
        if($holidays &= GermanHolidays::NEW_YEARS_DAY && $germanHolidays->getNewYearsDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::EPIPHANY && $germanHolidays->getEpiphany()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::MAUNDY_THURSDAY && $germanHolidays->getMaundyThursday()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::GOOD_FRIDAY && $germanHolidays->getGoodFriday()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::EASTER_DAY && $germanHolidays->getEasterDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::EASTER_MONDAY && $germanHolidays->getEasterMonday()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::DAY_OF_WORK && $germanHolidays->getDayOfWork()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::ASCENSION_DAY && $germanHolidays->getAscensionDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::WHIT_SUN && $germanHolidays->getWhitsun()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::WHIT_MONDAY && $germanHolidays->getWhitMonday()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::CORPUS_CHRISTII && $germanHolidays->getCorpusChristi()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::GERMAN_UNIFICATION_DAY && $germanHolidays->getGermanUnificationDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::REFOMATION_DAY && $germanHolidays->getReformationDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::ALL_SAINTS_DAY && $germanHolidays->getAllSaintsDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::DAY_OF_REPENTANCE && $germanHolidays->getDayOfRepentance()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::CHRISTMAS_DAY && $germanHolidays->getChristmasDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::XMAS_DAY && $germanHolidays->getXmasDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::BOXING_DAY && $germanHolidays->getBoxingDay()->format('dmY') == $dateNow) {

            return true;
        } elseif($holidays & GermanHolidays::NEW_YEARS_EVE && $germanHolidays->getNewYearsEve()->format('dmY') == $dateNow) {

            return true;
        }
        return false;
    }

}