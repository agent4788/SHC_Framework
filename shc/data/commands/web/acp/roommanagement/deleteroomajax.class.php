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
 * loescht einen Benutzer
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteRoomAjax extends AjaxCommand {

    protected $premission = 'shc.acp.roomManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('roommanagement');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Raum Objekt laden
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $room = RoomEditor::getInstance()->getRoomById($roomId);

        //pruefen ob der Raum existiert
        if(!$room instanceof Room) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.roomManagement.form.error.id')));
            $this->data = $tpl->fetchString('editroomform.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            RoomEditor::getInstance()->removeRoom($roomId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.success.deleteRoom'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deleteroom.html');
    }

}