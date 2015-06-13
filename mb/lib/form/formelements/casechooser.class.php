<?php

namespace MB\Form\FormElements;

//Imports
use MB\Movie\Editor\MovieCaseEditor;
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld der Verpackung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CaseChooser extends Select {

    public function __construct($name, $selectedCase = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        $values = array();
        $cases = MovieCaseEditor::getInstance()->listMovieCases(MovieCaseEditor::SORT_BY_NAME);
        foreach($cases as $case) {

            /** @var $case \MB\Movie\MovieCase */
            $values[$case->getHash()] = array($case->getName(), ($case->getHash() == $selectedCase ? 1 : 0));
        }
        $this->setValues($values);
    }
}