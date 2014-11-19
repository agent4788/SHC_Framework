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

    /**
     * alle Schaltserver anzeigen
     *
     * @var Integer
     */
    const FILTER_ALL = 1;

    /**
     * nur Schaltserver anzeigen die Funksteckdosen schalten koennen
     *
     * @var Integer
     */
    const FILTER_RADIOSOCKETS = 2;

    /**
     * nur Schaltserver anzeigen deren GPIOs gelesen werden koennen
     *
     * @var Integer
     */
    const FILTER_WRITEGPIO = 4;

    /**
     * nur Schaltserver anzeigen deren GPIOs geschrieben werden koennen
     *
     * @var Integer
     */
    const FILTER_READGPIO = 8;

    public function __construct($name, $switchServerId = null, $filter = self::FILTER_ALL) {

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
            if($filter & self::FILTER_ALL) {

                $values[$switchServer->getId()] = array($switchServer->getName() . $model, ($switchServer->getId() == $switchServerId ? 1 : 0));
            } elseif($filter & self::FILTER_ALL) {

                $values[$switchServer->getId()] = array($switchServer->getName() . $model, ($switchServer->getId() == $switchServerId ? 1 : 0));
            } elseif($filter & self::FILTER_RADIOSOCKETS && $switchServer->isRadioSocketsEnabled()) {

                $values[$switchServer->getId()] = array($switchServer->getName() . $model, ($switchServer->getId() == $switchServerId ? 1 : 0));
            } elseif($filter & self::FILTER_READGPIO && $switchServer->isReadGpiosEnabled()) {

                $values[$switchServer->getId()] = array($switchServer->getName() . $model, ($switchServer->getId() == $switchServerId ? 1 : 0));
            } elseif($filter & self::FILTER_WRITEGPIO && $switchServer->isWriteGpiosEnabled()) {

                $values[$switchServer->getId()] = array($switchServer->getName() . $model, ($switchServer->getId() == $switchServerId ? 1 : 0));
            }

        }
        $this->setValues($values);
    }
}