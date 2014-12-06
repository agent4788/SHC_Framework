<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use SHC\Timer\SwitchPoint;

/**
 * Auswahlfeld des Schaltbefehls fuer Schaltpunkte
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchPointCommandChooser extends Select {

    public function __construct($name, $command = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Wert estzen
        $this->setValues(array(
            SwitchPoint::SWITCH_ON => array(RWF::getLanguage()->get('global.on'), ($command === SwitchPoint::SWITCH_ON || $command === null ? 1 :0)),
            SwitchPoint::SWITCH_OFF  => array(RWF::getLanguage()->get('global.off'), ($command === SwitchPoint::SWITCH_OFF ? 1 :0)),
            SwitchPoint::SWITCH_TOGGLE  => array(RWF::getLanguage()->get('global.toggle'), ($command === SwitchPoint::SWITCH_TOGGLE ? 1 :0))
        ));
    }
}