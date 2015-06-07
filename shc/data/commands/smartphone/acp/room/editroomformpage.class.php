<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\RoomForm;
use SHC\Form\Forms\UserGroupForm;
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

    protected $template = 'editroomform.html';

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

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listrooms');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Raum Objekt laden
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $room = RoomEditor::getInstance()->getRoomById($roomId);

        //pruefen ob der Raum existiert
        if(!$room instanceof Room) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.roomManagement.form.error.id')));
            return;
        }

        //Formular erstellen
        $roomForm = new RoomForm($room);
        $roomForm->setView(UserGroupForm::SMARTPHONE_VIEW);
        $roomForm->setAction('index.php?app=shc&m&page=editroomform&id='. $room->getId());
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
            $this->response->addLocationHeader('index.php?app=shc&m&page=listrooms');
            $this->response->setBody('');
            $this->template = '';
        }

    }
}