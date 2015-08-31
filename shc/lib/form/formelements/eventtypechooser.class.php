<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use SHC\Event\EventEditor;

/**
 * Auswahlfeld des Event Typs
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EventTypeChooser extends Select {

    public function __construct($name) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        RWF::getLanguage()->disableAutoHtmlEndocde();
        $values = array(
            EventEditor::EVENT_HUMIDITY_CLIMB => RWF::getLanguage()->get('acp.eventsManagement.events.HumidityClimbOver'),
            EventEditor::EVENT_HUMIDITY_FALLS=> RWF::getLanguage()->get('acp.eventsManagement.events.HumidityFallsBelow'),
            EventEditor::EVENT_INPUT_HIGH => RWF::getLanguage()->get('acp.eventsManagement.events.InputHigh'),
            EventEditor::EVENT_INPUT_LOW => RWF::getLanguage()->get('acp.eventsManagement.events.InputLow'),
            EventEditor::EVENT_LIGHTINTENSITY_CLIMB => RWF::getLanguage()->get('acp.eventsManagement.events.LightIntensityClimbOver'),
            EventEditor::EVENT_LIGHTINTENSITY_FALLS => RWF::getLanguage()->get('acp.eventsManagement.events.LightIntensityFallBelow'),
            EventEditor::EVENT_MOISTURE_CLIMB => RWF::getLanguage()->get('acp.eventsManagement.events.MoistureClimbOver'),
            EventEditor::EVENT_MOISTURE_FALLS => RWF::getLanguage()->get('acp.eventsManagement.events.MoistureFallsBelow'),
            EventEditor::EVENT_TEMPERATURE_CLIMB => RWF::getLanguage()->get('acp.eventsManagement.events.TemperatureClimbOver'),
            EventEditor::EVENT_TEMPERATURE_FALLS => RWF::getLanguage()->get('acp.eventsManagement.events.TemperatureFallsBelow'),
            EventEditor::EVENT_USER_COMES_HOME => RWF::getLanguage()->get('acp.eventsManagement.events.UserComesHome'),
            EventEditor::EVENT_USER_LEAVE_HOME => RWF::getLanguage()->get('acp.eventsManagement.events.UserLeavesHome'),
            EventEditor::EVENT_SUNRISE => RWF::getLanguage()->get('acp.eventsManagement.events.Sunrise'),
            EventEditor::EVENT_SUNSET => RWF::getLanguage()->get('acp.eventsManagement.events.Sunset'),
            EventEditor::EVENT_FILE_CREATE => RWF::getLanguage()->get('acp.eventsManagement.events.FileCreate'),
            EventEditor::EVENT_FILE_DELETE => RWF::getLanguage()->get('acp.eventsManagement.events.FileDelete')
        );
        RWF::getLanguage()->enableAutoHtmlEndocde();
        $this->setValues($values);
    }
}