<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\User\UserEditor;
use RWF\Util\DataTypeUtil;
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

        //Benutzeranmeldung
        $rwfUser = UserEditor::getInstance()->getGuest();
        if(RWF::getRequest()->issetParam('user', Request::GET) && RWF::getRequest()->issetParam('password', Request::GET)) {

            $userName = RWF::getRequest()->getParam('user', Request::GET, DataTypeUtil::PLAIN);
            $password = RWF::getRequest()->getParam('password', Request::GET, DataTypeUtil::PLAIN);

            $user = UserEditor::getInstance()->getUserByName($userName);
            if($user != null && $user->checkPasswordHash($password)) {

                $rwfUser = $user;
            }
        }

        $roomsData = array();
        foreach($rooms as $room) {

            /* @var $room \SHC\Room\Room */
            if($room->isEnabled() && $room->isUserEntitled($rwfUser)) {

                $roomsData[] = array(
                    'id' => $room->getId(),
                    'name' => $room->getName()
                );
            }
        }
        $this->data = $roomsData;
    }
}