<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;
use RWF\User\User;
use RWF\User\UserEditor;
use SHC\SwitchServer\SwitchServerEditor;


/**
 * Auswahlfeld der Hauptbenutzergruppe
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServerChooser extends Select {

    public function __construct($name, $switchServerId = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $values = array();
        foreach(SwitchServerEditor::getInstance()->listSwitchServers(SwitchServerEditor::SORT_BY_NAME) as $switchServer) {

            $values[$switchServer->getId()] = array($switchServer->getName(), ($switchServer->getId() == $switchServerId ? 1 : 0));

        }
        $this->setValues($values);
    }
}