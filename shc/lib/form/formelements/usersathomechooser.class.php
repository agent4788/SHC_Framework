<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultiple;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * Auswahlfeld der Hauptbenutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UsersAtHomeChooser extends SelectMultiple {

    public function __construct($name, $usersAtHome = array()) {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array(
            'size' => 10
        ));

        //Gruppen anmelden
        $values = array();
        foreach(UserAtHomeEditor::getInstance()->listUsersAtHome(UserAtHomeEditor::SORT_BY_ORDER_ID) as $userAtHome) {

            $values[$userAtHome->getId()] = array($userAtHome->getName(), (in_array($userAtHome->getId(), $usersAtHome) ? 1 : 0));

        }
        $this->setValues($values);
    }
}