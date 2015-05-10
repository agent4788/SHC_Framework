<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;
use RWF\Util\FileUtil;

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

        //Gruppen anmelden
        $values = array(
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
            'shc-icon-rollo' => array('shc-icon-rollo', ($icon == 'shc-icon-rollo' ? 1 : 0))
        );
        $this->setValues($values);
    }
}