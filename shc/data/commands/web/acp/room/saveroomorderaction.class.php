<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Room\Room;
use SHC\Room\RoomEditor;

/**
 * Speichert die Sortierung der Raeume
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SaveRoomOrderAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.roomManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&page=listrooms';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('roommanagement', 'form');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        $r = $this->request;

        //Meldung
        $message = new Message();

        //eingabedaten Pruefen
        $order = $r->getParam('roomOrder', Request::POST);
        $filteredOrder = array();
        $valid = true;
        foreach($order as $id => $orderId) {

            $room = RoomEditor::getInstance()->getRoomById($id);
            if($room instanceof Room) {

                $filteredOrder[$id] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
            } else {

                //Fehlerhafte Eingaben
                $valid = false;
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                break;
            }
        }

        //Speichern
        if($valid === true) {

            try {

                RoomEditor::getInstance()->editRoomOrder($filteredOrder);
                RoomEditor::getInstance()->loadData();
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.success.orderRoom'));
            } catch (\Exception $e) {

                //Fehler beim speichern
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.error.order'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}