<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultiple;
use SHC\Switchable\Readable;
use SHC\Switchable\SwitchableEditor;

/**
 * Auswahlfeld fuer Eingaenge
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class InputChooser extends SelectMultiple {

    public function __construct($name, $selectedInputs = array()) {

        //Allgemeine Daten
        $this->setName($name);

        //Styles setzen
        $values = array();
        foreach(SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_NOTHING) as $readable) {

            if($readable instanceof Readable) {

                $values[$readable->getId()] = array($readable->getName(), (in_array($readable->getId(), $selectedInputs) ? 1 : 0));
            }
        }
        $this->setValues($values);
    }
}