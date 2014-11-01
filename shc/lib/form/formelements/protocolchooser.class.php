<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld des Protokolls
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ProtocolChooser extends Select {

    public function __construct($name, $protocol = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $values = array(
            'elro_he' => array('elro_he', ($protocol == 'elro_he' ? 1 : 0))
        );
        $this->setValues($values);
    }
}