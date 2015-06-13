<?php

namespace MB\Form\FormElements;

//Imports
use MB\Movie\Editor\MovieTypeEditor;
use RWF\Form\FormElements\Select;

/**
 * Auswahlfeld des Mediums
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class TypeChooser extends Select {

    public function __construct($name, $selectedType = null) {

        //Allgemeine Daten
        $this->setName($name);

        //Auswahl
        $values = array();
        $types = MovieTypeEditor::getInstance()->listMovieTypes(MovieTypeEditor::SORT_BY_NAME);
        foreach($types as $type) {

            /** @var $type \MB\Movie\MovieType */
            $values[$type->getHash()] = array($type->getName(), ($type->getHash() == $selectedType ? 1 : 0));
        }
        $this->setValues($values);
    }
}