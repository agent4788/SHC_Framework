<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\Select;
use RWF\Runtime\RaspberryPi;
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

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            $model = '';
            if($switchServer->getModel() == RaspberryPi::MODEL_A) {

                $model = ' (Model A)';
            } elseif($switchServer->getModel() == RaspberryPi::MODEL_B) {

                $model = ' (Model B)';
            } elseif($switchServer->getModel() == RaspberryPi::MODEL_B_PLUS) {

                $model = ' (Model B+)';
            }
            $values[$switchServer->getId()] = array($switchServer->getName() . $model, ($switchServer->getId() == $switchServerId ? 1 : 0));

        }
        $this->setValues($values);
    }
}