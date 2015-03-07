<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Date\Calendar\Holidays\GermanHolidays;
use RWF\Form\FormElements\SelectMultiple;
use SHC\Core\SHC;

/**
 * Auswahlfeld des Icons
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class HolidayChooser extends SelectMultiple {

    public function __construct($name, $holidays = null) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array(
            'size' => 10
        ));

        //Gruppen anmelden
        $values = array(
            GermanHolidays::NEW_YEARS_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.NEW_YEARS_DAY'), ($holidays & GermanHolidays::NEW_YEARS_DAY ? 1 : 0)),
            GermanHolidays::EPIPHANY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.EPIPHANY'), ($holidays & GermanHolidays::EPIPHANY ? 1 : 0)),
            GermanHolidays::MAUNDY_THURSDAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.MAUNDY_THURSDAY'), ($holidays & GermanHolidays::MAUNDY_THURSDAY ? 1 : 0)),
            GermanHolidays::GOOD_FRIDAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.GOOD_FRIDAY'), ($holidays & GermanHolidays::GOOD_FRIDAY ? 1 : 0)),
            GermanHolidays::EASTER_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.EASTER_DAY'), ($holidays & GermanHolidays::EASTER_DAY ? 1 : 0)),
            GermanHolidays::EASTER_MONDAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.EASTER_MONDAY'), ($holidays & GermanHolidays::EASTER_MONDAY ? 1 : 0)),
            GermanHolidays::DAY_OF_WORK => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.DAY_OF_WORK'), ($holidays & GermanHolidays::DAY_OF_WORK ? 1 : 0)),
            GermanHolidays::ASCENSION_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.ASCENSION_DAY'), ($holidays & GermanHolidays::ASCENSION_DAY ? 1 : 0)),
            GermanHolidays::WHIT_SUN => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.WHIT_SUN'), ($holidays & GermanHolidays::WHIT_SUN ? 1 : 0)),
            GermanHolidays::WHIT_MONDAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.WHIT_MONDAY'), ($holidays & GermanHolidays::WHIT_MONDAY ? 1 : 0)),
            GermanHolidays::CORPUS_CHRISTII => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.CORPUS_CHRISTII'), ($holidays & GermanHolidays::CORPUS_CHRISTII ? 1 : 0)),
            GermanHolidays::GERMAN_UNIFICATION_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.GERMAN_UNIFICATION_DAY'), ($holidays & GermanHolidays::GERMAN_UNIFICATION_DAY ? 1 : 0)),
            GermanHolidays::REFOMATION_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.REFOMATION_DAY'), ($holidays & GermanHolidays::REFOMATION_DAY ? 1 : 0)),
            GermanHolidays::ALL_SAINTS_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.ALL_SAINTS_DAY'), ($holidays & GermanHolidays::ALL_SAINTS_DAY ? 1 : 0)),
            GermanHolidays::DAY_OF_REPENTANCE => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.DAY_OF_REPENTANCE'), ($holidays & GermanHolidays::DAY_OF_REPENTANCE ? 1 : 0)),
            GermanHolidays::CHRISTMAS_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.CHRISTMAS_DAY'), ($holidays & GermanHolidays::CHRISTMAS_DAY ? 1 : 0)),
            GermanHolidays::XMAS_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.XMAS_DAY'), ($holidays & GermanHolidays::XMAS_DAY ? 1 : 0)),
            GermanHolidays::BOXING_DAY => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.BOXING_DAY'), ($holidays & GermanHolidays::BOXING_DAY ? 1 : 0)),
            GermanHolidays::NEW_YEARS_EVE => array(SHC::getLanguage()->get('acp.conditionManagement.form.holiday.NEW_YEARS_EVE'), ($holidays & GermanHolidays::NEW_YEARS_EVE ? 1 : 0))
        );
        $this->setValues($values);
    }

    /**
     * gibt die berechnete Ganzzahl der Feiertage zurueck
     *
     * @return Integer
     */
    public function getHolidays() {

        $values = $this->getValues();
        $return = 0;
        foreach($values as $value) {

            $return += intval($value);
        }
        return $return;
    }
}