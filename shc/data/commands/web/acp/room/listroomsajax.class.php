<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Room\Room;
use SHC\Room\RoomEditor;


/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListRoomsAjax extends AjaxCommand {

    protected $premission = 'shc.acp.roomManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('roommanagement', 'acpindex', 'form');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Sortierung speichern
        $r = $this->request;
        if($r->issetParam('req', Request::GET) && $r->getParam('req', Request::GET, DataTypeUtil::STRING) == 'saveorder') {

            //Meldung
            $message = new Message();

            //eingabedaten Pruefen
            $order = $r->getParam('roomOrder', Request::POST);
            $filteredOrder = array();
            $valid = true;
            foreach($order as $id => $orderId) {

                $room = RoomEditor::getInstance()->getRoomById($id);
                if($room instanceof Room ) {

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
                    $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.error.order'));
                } catch (\Exception $e) {

                    //Fehler beim speichern
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.error.order'));
                }
            }
            $tpl->assign('message', $message);
        }

        //Raeume auflisten
        $tpl->assign('roomList', RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID));
        $this->data = $tpl->fetchString('listrooms.html');
    }

}