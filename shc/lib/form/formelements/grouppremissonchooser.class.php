<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Core\RWF;
use RWF\Form\FormElements\SelectMultipleWithEmptyElement;
use RWF\User\User;
use RWF\User\UserEditor;

/**
 * Auswahl fuer benutzerrechte
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class GroupPremissonChooser extends SelectMultipleWithEmptyElement {

    /**
     * @param String $name        Feld Name
     * @param Array  $selectedIds Ausgewaehlte IDs
     */
    public function __construct($name, $selectedIds = array()) {

        $this->setName($name);

        //Optionen setzen
        $options = array(
            'emptyLabel' => RWF::getLanguage()->get('acpindex.allUsers'),
            'emptySelected' => false
        );
        if(!count($selectedIds) || (isset($selectedIds[0]) && $selectedIds[0] == '')) {

            $options['emptySelected'] = true;
        }
        $this->setOptions($options);

        //Daten laden
        $values = array();
        $groupList = UserEditor::getInstance()->listUserGruops(UserEditor::SORT_BY_NAME);
        foreach($groupList as $group) {

            $values[$group->getId()] = array($group->getName(), (in_array($group->getId(), $selectedIds) ? 1 : 0));
        }
        $this->setValues($values);
    }

    /**
     * gibt eine Liste mit den erlaubten Benutzergruppen zurueck
     *
     * @return Array
     */
    public function getAllowedGroups() {

        $values = $this->getValues();
        if($values[0] === null) {

            return array('');
        }
        return $values;
    }
}