<?php

namespace MB\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultipleWithEmptyElement;
use RWF\User\User;
use RWF\User\UserEditor;
use RWF\User\UserGroup;


/**
 * Auswahlfeld die Benutzergruppen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class UserGroupChooser extends SelectMultipleWithEmptyElement {

    public function __construct($name, User $user = null)
    {

        //Allgemeine Daten
        $this->setName($name);
        $this->setOptions(array(
            'emptySelected' => false,
            'size' => 5
        ));

        //Gruppen Liste Vorbereiten
        $groups = array();
        if ($user instanceof User) {

            foreach ($user->listGroups() as $group) {

                if($group instanceof UserGroup) {

                    $groups[] = $group->getId();
                }
            }
        }

        //leeres Element selektieren
        if(!$user instanceof User || count($groups) < 1) {

            $this->options['emptySelected'] = true;
        }

        //Gruppen anmelden
        $values = array();
        foreach(UserEditor::getInstance()->listUserGruops(UserEditor::SORT_BY_NAME) as $userGroup) {

            $values[$userGroup->getId()] = array($userGroup->getName(), (in_array($userGroup->getId(), $groups) ? 1 : 0));

        }
        $this->setValues($values);
    }
}