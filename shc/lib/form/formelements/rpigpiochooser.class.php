<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;


/**
 * Auswahlfeld der Hauptbenutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RPiGpioChooser extends Select {

    public function __construct($name, $gpioPin = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $values = array();
        foreach(range(0, 100) as $i) {

            $values[$i] = array($i, ($i == $gpioPin ? 1 : 0));

        }
        $this->setValues($values);
    }
}