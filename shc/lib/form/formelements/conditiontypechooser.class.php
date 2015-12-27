<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld des Typs einer Bedingung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.2-0
 */
class ConditionTypeChooser extends Select {

    public function __construct($name) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        RWF::getLanguage()->disableAutoHtmlEndocde();
        $values = array(
             1 => RWF::getLanguage()->get('acp.conditionManagement.condition.HumidityGreaterThanCondition'),
             2 => RWF::getLanguage()->get('acp.conditionManagement.condition.HumidityLowerThanCondition'),
             3 => RWF::getLanguage()->get('acp.conditionManagement.condition.LightIntensityGreaterThanCondition'),
             4 => RWF::getLanguage()->get('acp.conditionManagement.condition.LightIntensityLowerThanCondition'),
             5 => RWF::getLanguage()->get('acp.conditionManagement.condition.MoistureGreaterThanCondition'),
             6 => RWF::getLanguage()->get('acp.conditionManagement.condition.MoistureLowerThanCondition'),
             7 => RWF::getLanguage()->get('acp.conditionManagement.condition.TemperatureGreaterThanCondition'),
             8 => RWF::getLanguage()->get('acp.conditionManagement.condition.TemperatureLowerThanCondition'),
             9 => RWF::getLanguage()->get('acp.conditionManagement.condition.NobodyAtHomeCondition'),
            10 => RWF::getLanguage()->get('acp.conditionManagement.condition.UserAtHomeCondition'),
            21 => RWF::getLanguage()->get('acp.conditionManagement.condition.UserNotAtHomeCondition'),
            11 => RWF::getLanguage()->get('acp.conditionManagement.condition.DateCondition'),
            12 => RWF::getLanguage()->get('acp.conditionManagement.condition.DayOfWeekCondition'),
            13 => RWF::getLanguage()->get('acp.conditionManagement.condition.TimeOfDayCondition'),
            14 => RWF::getLanguage()->get('acp.conditionManagement.condition.SunriseSunsetCondition'),
            15 => RWF::getLanguage()->get('acp.conditionManagement.condition.SunsetSunriseCondition'),
            16 => RWF::getLanguage()->get('acp.conditionManagement.condition.FileExistsCondition'),
            17 => RWF::getLanguage()->get('acp.conditionManagement.condition.HolidaysCondition'),
            18 => RWF::getLanguage()->get('acp.conditionManagement.condition.InputHighCondition'),
            19 => RWF::getLanguage()->get('acp.conditionManagement.condition.InputLowCondition'),
            20 => RWF::getLanguage()->get('acp.conditionManagement.condition.FirstLoopCondition'),
            22 => RWF::getLanguage()->get('acp.conditionManagement.condition.JustCalendarWeekCondition'),
            23 => RWF::getLanguage()->get('acp.conditionManagement.condition.OddCalendarWeekCondition'),
            24 => RWF::getLanguage()->get('acp.conditionManagement.condition.SwitchableStateHighCondition'),
            25 => RWF::getLanguage()->get('acp.conditionManagement.condition.SwitchableStateLowCondition'),
        );
        RWF::getLanguage()->enableAutoHtmlEndocde();
        $this->setValues($values);
    }
}