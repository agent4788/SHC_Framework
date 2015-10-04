<?php

namespace MB\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;
use RWF\User\User;
use RWF\User\UserEditor;


/**
 * Auswahlfeld der Hauptbenutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserMainGroupChooser extends Select {

    public function __construct($name, User $user = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $values = array();
        foreach(UserEditor::getInstance()->listUserGruops(UserEditor::SORT_BY_NAME) as $userGroup) {

            $values[$userGroup->getId()] = array($userGroup->getName(), ($user instanceof User && $user->getMainGroup()->getId() == $userGroup->getId() ? 1 : 0));

        }
        $this->setValues($values);
    }
}