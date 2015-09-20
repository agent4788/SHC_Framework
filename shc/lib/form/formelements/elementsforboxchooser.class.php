<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\SelectMultiple;

use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;
use SHC\View\Room\ViewHelperBox;

/**
 * Auswahlfeld der Hauptbenutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ElementsForBoxChooser extends SelectMultiple {

    public function __construct($name, ViewHelperBox $box) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array(
            'size' => 10
        ));

        //Auswahl
        $values = array();

        //lebare/schaltbare Elemente
        foreach(SwitchableEditor::getInstance()->listElementsForRoom($box->getRoomId()) as $element) {

            $type = '';
            if($element instanceof Activity) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.activity');
            }elseif($element instanceof Countdown) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.countdown');
            } elseif($element instanceof RadioSocket) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.radiosocket');
            } elseif($element instanceof RpiGpioInput) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.rpiGpioInput');
            } elseif($element instanceof RpiGpioOutput) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.rpiGpioOutput');
            } elseif($element instanceof WakeOnLan) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.wakeOnLan');
            } elseif($element instanceof Reboot) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.reboot');
            } elseif($element instanceof Shutdown) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.shutdown');
            } elseif($element instanceof Script) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.script');
            } elseif($element instanceof AvmSocket) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.avmSocket');
            } elseif($element instanceof FritzBox) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.fritzBox');
            }
            $values['element-'. $element->getId()] = array($element->getName() .' ('. $type .')', $box->isElementInBox($element));
        }

        //Sensoren
        foreach(SensorPointEditor::getInstance()->listSensors() as $sensor) {

            //pruefen ob der Sensor dem Raum zugeordnet ist
            /* @var $sensor \SHC\Sensor\Sensor  */
            if(!$sensor->isInRoom($box->getRoomId())) {

                continue;
            }

            $type = '';
            if($sensor instanceof BMP) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.BMP');
            } elseif($sensor instanceof DHT) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.DHT');
            } elseif($sensor instanceof DS18x20) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.DS18x20');
            } elseif($sensor instanceof Hygrometer) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.Hygrometer');
            } elseif($sensor instanceof RainSensor) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.RainSensor');
            } elseif($sensor instanceof LDR) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.LDR');
            } elseif($sensor instanceof AvmMeasuringSocket) {

                $type = RWF::getLanguage()->get('acp.switchableManagement.element.avmMeasuringSocket');
            }
            $values['sensor-'. $sensor->getId()] = array($sensor->getName() .' ('. $type .')', $box->isElementInBox($sensor));
        }

        $this->setValues($values);
    }
}