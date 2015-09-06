<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultipleWithEmptyElement;
use SHC\Condition\ConditionEditor;


/**
 * Auswahl fuer Schaltpunkte
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ConditionsChooser extends SelectMultipleWithEmptyElement {

    /**
     * @param String $name         Feld Name
     * @param Array  $conditions   Ausgewaehlte IDs
     */
    public function __construct($name, $conditions = array()) {

        $this->setName($name);

        //Liste mit IDs vorbereiten
        $conditionsIDs = array();
        foreach($conditions as $condition) {

            $conditionsIDs[] = $condition->getId();
        }

        //Optionen setzen
        $options = array(
            'emptySelected' => false,
            'size' => 10
        );
        if(!count($conditionsIDs) || (isset($conditionsIDs[0]) && $conditionsIDs[0] == '')) {

            $options['emptySelected'] = true;
        }
        $this->setOptions($options);

        //Daten laden
        $values = array();
        $conditionsList = ConditionEditor::getInstance()->listConditions(ConditionEditor::SORT_BY_NAME);
        foreach($conditionsList as $condition) {

            $values[$condition->getId()] = array($condition->getName(), (in_array($condition->getId(), $conditionsIDs) ? 1 : 0));
        }
        $this->setValues($values);
    }
}