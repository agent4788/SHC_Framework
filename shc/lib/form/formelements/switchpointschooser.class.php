<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultipleWithEmptyElement;
use SHC\Timer\SwitchPointEditor;

/**
 * Auswahl fuer Schaltpunkte
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchPointsChooser extends SelectMultipleWithEmptyElement {

    /**
     * @param String $name         Feld Name
     * @param Array  $switchPoints Ausgewaehlte IDs
     */
    public function __construct($name, $switchPoints = array()) {

        $this->setName($name);

        //Liste mit IDs vorbereiten
        $switchPointIds = array();
        foreach($switchPoints as $switchPoint) {

            $switchPointIds[] = $switchPoint->getId();
        }

        //Optionen setzen
        $options = array(
            'emptySelected' => false,
            'size' => 10
        );
        if(!count($switchPointIds) || (isset($switchPointIds[0]) && $switchPointIds[0] == '')) {

            $options['emptySelected'] = true;
        }
        $this->setOptions($options);

        //Daten laden
        $values = array();
        $switchPointList = SwitchPointEditor::getInstance()->listSwitchPoints(SwitchPointEditor::SORT_BY_NAME);
        foreach($switchPointList as $switchPoint) {

            $values[$switchPoint->getId()] = array($switchPoint->getName(), (in_array($switchPoint->getId(), $switchPointIds) ? 1 : 0));
        }
        $this->setValues($values);
    }
}