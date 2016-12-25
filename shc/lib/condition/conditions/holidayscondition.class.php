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

            throw new \Exception('Die Feiertage müssen angegeben werden', 1580);
        }

        $invert = false;
        if(isset($this->data['invert']) && $this->data['invert'] == true) {

            $invert = true;
        }

        $holidays = $this->data['holidays'];
        $now = DateTime::now();
        $dateNow = $now->format('dmY');
        $germanHolidays = new GermanHolidays($now->getYear());
        if(($holidays & GermanHolidays::NEW_YEARS_DAY) == GermanHolidays::NEW_YEARS_DAY && $germanHolidays->getNewYearsDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::EPIPHANY) == GermanHolidays::EPIPHANY && $germanHolidays->getEpiphany()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::MAUNDY_THURSDAY) == GermanHolidays::MAUNDY_THURSDAY && $germanHolidays->getMaundyThursday()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::GOOD_FRIDAY) == GermanHolidays::GOOD_FRIDAY && $germanHolidays->getGoodFriday()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::EASTER_DAY) == GermanHolidays::EASTER_DAY && $germanHolidays->getEasterDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::EASTER_MONDAY) == GermanHolidays::EASTER_MONDAY && $germanHolidays->getEasterMonday()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::DAY_OF_WORK) == GermanHolidays::DAY_OF_WORK && $germanHolidays->getDayOfWork()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::ASCENSION_DAY) == GermanHolidays::ASCENSION_DAY && $germanHolidays->getAscensionDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::WHIT_SUN) == GermanHolidays::WHIT_SUN && $germanHolidays->getWhitsun()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::WHIT_MONDAY) == GermanHolidays::WHIT_MONDAY && $germanHolidays->getWhitMonday()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::CORPUS_CHRISTI) == GermanHolidays::CORPUS_CHRISTI && $germanHolidays->getCorpusChristi()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::ASSUMPTION) == GermanHolidays::ASSUMPTION && $germanHolidays->getAssumption()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::GERMAN_UNIFICATION_DAY) == GermanHolidays::GERMAN_UNIFICATION_DAY && $germanHolidays->getGermanUnificationDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::REFOMATION_DAY) == GermanHolidays::REFOMATION_DAY && $germanHolidays->getReformationDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::ALL_SAINTS_DAY) == GermanHolidays::ALL_SAINTS_DAY && $germanHolidays->getAllSaintsDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::DAY_OF_REPENTANCE) == GermanHolidays::DAY_OF_REPENTANCE && $germanHolidays->getDayOfRepentance()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::CHRISTMAS_DAY) == GermanHolidays::CHRISTMAS_DAY && $germanHolidays->getChristmasDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::XMAS_DAY) == GermanHolidays::XMAS_DAY && $germanHolidays->getXmasDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::BOXING_DAY) == GermanHolidays::BOXING_DAY && $germanHolidays->getBoxingDay()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        } elseif(($holidays & GermanHolidays::NEW_YEARS_EVE) == GermanHolidays::NEW_YEARS_EVE && $germanHolidays->getNewYearsEve()->format('dmY') == $dateNow) {

            if($invert == false) {

                return true;
            }
            return false;
        }
        if($invert == false) {

            return false;
        }
        return true;
    }

}