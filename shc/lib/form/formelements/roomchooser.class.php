<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultipleWithEmptyElement;
use SHC\Core\SHC;
use SHC\Room\RoomEditor;

/**
 * Auswahlfeld des Raumes
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomChooser extends SelectMultipleWithEmptyElement {

    public function __construct($name, array $rooms = array()) {

        //Allgemeine Daten
        $this->setName($name);

        //Gruppen anmelden
        $values = array();
        $nothingSelected = true;
        foreach(RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID) as $room) {

            $values[$room->getId()] = array($room->getName(), (in_array($room->getId(), $rooms) ? 1 : 0));

            if(in_array($room->getId(), $rooms)) {

                $nothingSelected = false;
            }
        }
        $this->setValues($values);

        //Optionen
        $this->setOptions(array(
            'emptyLabel' => SHC::getLanguage()->get('acpindex.roomLess'),
            'emptySelected' => $nothingSelected
        ));
    }
}