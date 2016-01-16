<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;
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
class IconChooser extends Select {

    public function __construct($name, $icon = null) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array('grouped' => true));

        //Gruppen anmelden
        $values = array(
            SHC::getLanguage()->get('acp.switchableManagement.switchables') => array(
                'shc-icon-lamp' => array('shc-icon-lamp', ($icon == 'shc-icon-lamp' ? 1 : 0)),
                'shc-icon-flashlight' => array('shc-icon-flashlight', ($icon == 'shc-icon-flashlight' ? 1 : 0)),
                'shc-icon-power' => array('shc-icon-power', ($icon == 'shc-icon-power' ? 1 : 0)),
                'shc-icon-socket' => array('shc-icon-socket', ($icon == 'shc-icon-socket' ? 1 : 0)),
                'shc-icon-countdown' => array('shc-icon-countdown', ($icon == 'shc-icon-countdown' ? 1 : 0)),
                'shc-icon-chip' => array('shc-icon-chip', ($icon == 'shc-icon-chip' ? 1 : 0)),
                'shc-icon-clock' => array('shc-icon-clock', ($icon == 'shc-icon-clock' ? 1 : 0)),
                'shc-icon-monitor' => array('shc-icon-monitor', ($icon == 'shc-icon-monitor' ? 1 : 0)),
                'shc-icon-nas' => array('shc-icon-nas', ($icon == 'shc-icon-nas' ? 1 : 0)),
                'shc-icon-printer' => array('shc-icon-printer', ($icon == 'shc-icon-printer' ? 1 : 0)),
                'shc-icon-tv' => array('shc-icon-tv', ($icon == 'shc-icon-tv' ? 1 : 0)),
                'shc-icon-waterBoiler' => array('shc-icon-waterBoiler', ($icon == 'shc-icon-waterBoiler' ? 1 : 0)),
                'shc-icon-coffee' => array('shc-icon-coffee', ($icon == 'shc-icon-coffee' ? 1 : 0)),
                'shc-icon-rhythmbox' => array('shc-icon-rhythmbox', ($icon == 'shc-icon-rhythmbox' ? 1 : 0)),
                'shc-icon-christmasTree' => array('shc-icon-christmasTree', ($icon == 'shc-icon-christmasTree' ? 1 : 0)),
                'shc-icon-candles' => array('shc-icon-candles', ($icon == 'shc-icon-candles' ? 1 : 0)),
                'shc-icon-christmasLights' => array('shc-icon-christmasLights', ($icon == 'shc-icon-christmasLights' ? 1 : 0)),
                'shc-icon-star' => array('shc-icon-star', ($icon == 'shc-icon-star' ? 1 : 0)),
                'shc-icon-rollo' => array('shc-icon-rollo', ($icon == 'shc-icon-rollo' ? 1 : 0)),
                'shc-icon-camera' => array('shc-icon-camera', ($icon == 'shc-icon-camera' ? 1 : 0)),
                'shc-icon-camera2' => array('shc-icon-camera2', ($icon == 'shc-icon-camera2' ? 1 : 0))
            ),
            SHC::getLanguage()->get('acp.switchableManagement.sensors') => array(
                'shc-icon-ds18x20' => array('shc-icon-ds18x20', ($icon == 'shc-icon-ds18x20' ? 1 : 0)),
                'shc-icon-dht' => array('shc-icon-dht', ($icon == 'shc-icon-dht' ? 1 : 0)),
                'shc-icon-bmp' => array('shc-icon-bmp', ($icon == 'shc-icon-bmp' ? 1 : 0)),
                'shc-icon-rain' => array('shc-icon-rain', ($icon == 'shc-icon-rain' ? 1 : 0)),
                'shc-icon-hygrometer' => array('shc-icon-hygrometer', ($icon == 'shc-icon-hygrometer' ? 1 : 0)),
                'shc-icon-ldr' => array('shc-icon-ldr', ($icon == 'shc-icon-ldr' ? 1 : 0)),
                'shc-icon-avmPowerSensor' => array('shc-icon-avmPowerSensor', ($icon == 'shc-icon-avmPowerSensor' ? 1 : 0)),
                'shc-icon-edimaxPowerSensor' => array('shc-icon-edimaxPowerSensor', ($icon == 'shc-icon-edimaxPowerSensor' ? 1 : 0)),
                'shc-icon-gasmeter' => array('shc-icon-gasmeter', ($icon == 'shc-icon-gasmeter' ? 1 : 0)),
                'shc-icon-hcsr04' => array('shc-icon-hcsr04', ($icon == 'shc-icon-hcsr04' ? 1 : 0)),
                'shc-icon-sct013' => array('shc-icon-sct013', ($icon == 'shc-icon-sct013' ? 1 : 0)),
                'shc-icon-cometThermostat' => array('shc-icon-cometThermostat', ($icon == 'shc-icon-cometThermostat' ? 1 : 0)),
                'shc-icon-watermeter' => array('shc-icon-watermeter', ($icon == 'shc-icon-watermeter' ? 1 : 0))
            )
        );
        $this->setValues($values);
    }
}