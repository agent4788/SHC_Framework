<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use SHC\Room\RoomEditor;

/**
 * gibt eine Liste mit den Raeumen als JSON String zurueck
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomsJsonAjax extends AjaxCommand {

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Raeume laden
        $rooms = RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID);

        $roomsData = array();
        foreach($rooms as $room) {

            /* @var $room \SHC\Room\Room */
            //TODO Benutzer über URL Parameter erkennen
            if($room->isEnabled() && $room->isUserEntitled(RWF::getVisitor())) {

                $roomsData[] = array(
                    'id' => $room->getId(),
                    'name' => $room->getName()
                );
            }
        }
        $this->data = $roomsData;
    }
}