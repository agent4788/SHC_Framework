<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\Select;
use SHC\Switchable\AbstractSwitchable;

/**
 * Auswahlfeld des Schaltbefehls
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchCommandChooser extends Select {

    public function __construct($name, $command = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Wert estzen
        $this->setValues(array(
            AbstractSwitchable::STATE_ON => array(RWF::getLanguage()->get('global.on'), ($command === AbstractSwitchable::STATE_ON || $command === null ? 1 :0)),
            AbstractSwitchable::STATE_OFF  => array(RWF::getLanguage()->get('global.off'), ($command === AbstractSwitchable::STATE_OFF ? 1 :0))
        ));
    }
}