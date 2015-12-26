<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use SHC\Sensor\SensorPointEditor;
use SHC\Switchable\SwitchableEditor;

/**
 * Auswahlfeld der Hauptbenutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ElementTypeChooser extends Select {

    public function __construct($name) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array('grouped' => true));

        //Auswahl
        RWF::getLanguage()->disableAutoHtmlEndocde();
        $values = array(
            RWF::getLanguage()->get('acp.switchableManagement.switchables') => array(
                SwitchableEditor::TYPE_ACTIVITY => RWF::getLanguage()->get('acp.switchableManagement.element.activity'),
                SwitchableEditor::TYPE_COUNTDOWN => RWF::getLanguage()->get('acp.switchableManagement.element.countdown'),
                SwitchableEditor::TYPE_RADIOSOCKET => RWF::getLanguage()->get('acp.switchableManagement.element.radiosocket'),
                SwitchableEditor::TYPE_AVM_SOCKET => RWF::getLanguage()->get('acp.switchableManagement.element.avmSocket'),
                SwitchableEditor::TYPE_RPI_GPIO_INPUT => RWF::getLanguage()->get('acp.switchableManagement.element.rpiGpioInput'),
                SwitchableEditor::TYPE_RPI_GPIO_OUTPUT => RWF::getLanguage()->get('acp.switchableManagement.element.rpiGpioOutput'),
                SwitchableEditor::TYPE_WAKEONLAN => RWF::getLanguage()->get('acp.switchableManagement.element.wakeOnLan'),
                //SwitchableEditor::TYPE_RADIOSOCKET_DIMMER => RWF::getLanguage()->get('acp.switchableManagement.element.radioScketDimmer'),
                SwitchableEditor::TYPE_REBOOT => RWF::getLanguage()->get('acp.switchableManagement.element.reboot'),
                SwitchableEditor::TYPE_SHUTDOWN => RWF::getLanguage()->get('acp.switchableManagement.element.shutdown'),
                //SwitchableEditor::TYPE_REMOTE_REBOOT=> RWF::getLanguage()->get('acp.switchableManagement.element.remoteReboot'),
                //SwitchableEditor::TYPE_REMOTE_SHUTDOWN => RWF::getLanguage()->get('acp.switchableManagement.element.remoteShutdown'),
                SwitchableEditor::TYPE_SCRIPT => RWF::getLanguage()->get('acp.switchableManagement.element.script'),
                SwitchableEditor::TYPE_FRITZBOX => RWF::getLanguage()->get('acp.switchableManagement.element.fritzBox'),
                SwitchableEditor::TYPE_EDIMAX_SOCKET => RWF::getLanguage()->get('acp.switchableManagement.element.edimaxSocket'),
                SwitchableEditor::TYPE_VIRTUAL_SOCKET => RWF::getLanguage()->get('acp.switchableManagement.element.virtualSocket')
            ),
            RWF::getLanguage()->get('acp.switchableManagement.vSensors') => array(
                SensorPointEditor::VSENSOR_ENERGY => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.energy'),
                SensorPointEditor::VSENSOR_FLUID_AMOUNT => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.fluidAmount'),
                SensorPointEditor::VSENSOR_HUMIDITY => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.humidity'),
                SensorPointEditor::VSENSOR_LIGHT_INTENSITY => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.lightIntensity'),
                SensorPointEditor::VSENSOR_MOISTURE => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.moisture'),
                SensorPointEditor::VSENSOR_POWER => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.power'),
                SensorPointEditor::VSENSOR_TEMPERATURE => RWF::getLanguage()->get('acp.switchableManagement.element.vSensor.temperature')
            )
        );
        RWF::getLanguage()->enableAutoHtmlEndocde();
        $this->setValues($values);
    }
}