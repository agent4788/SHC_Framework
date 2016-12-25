<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\RoomForm;
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
class EditRoomFormPage extends PageCommand {

    protected $template = 'roomform.html';

    protected $requiredPremission = 'shc.acp.roomManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'roommanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //Raum Objekt laden
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $room = RoomEditor::getInstance()->getRoomById($roomId);

        //pruefen ob der Raum existiert
        if(!$room instanceof Room) {

            SHC::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.roomManagement.form.error.id')));
            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&page=listrooms');
            $this->response->setBody('');
            $this->template = '';
            return;
        }

        //Formular erstellen
        $roomForm = new RoomForm($room);
        $roomForm->setAction('index.php?app=shc&page=editroomform&id='. $room->getId());
        $roomForm->addId('shc-view-form-editRoom');

        if(!$roomForm->isSubmitted() || ($roomForm->isSubmitted() && !$roomForm->validate() === true)) {

            $tpl->assign('roomForm', $roomForm);
        } else {

            //Speichern
            $name = $roomForm->getElementByName('name')->getValue();
            $enabled = $roomForm->getElementByName('enabled')->getValue();
            $allowedUsers = $roomForm->getElementByName('allowedUsers')->getAllowedGroups();

            $message = new Message();
            try {

                RoomEditor::getInstance()->editRoom($roomId, $name, $enabled, $allowedUsers);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.success.editRoom'));
            } catch(\Exception $e) {

                if($e->getCode() == 1112) {

                    //Raumname schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.error.1500'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.error'));
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&page=listrooms');
            $this->response->setBody('');
            $this->template = '';
        }

    }
}