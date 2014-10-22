<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Util\Message;
use SHC\Form\Forms\RoomForm;
use SHC\Room\RoomEditor;

/**
 * bearbeitet einen Raum
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddRoomFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.roomManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('roommanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Formular erstellen
        $roomForm = new RoomForm();
        $roomForm->addId('shc-view-form-addRoom');

        if(!$roomForm->isSubmitted() || ($roomForm->isSubmitted() && !$roomForm->validate() === true)) {

            $tpl->assign('roomForm', $roomForm);
        } else {

            //Speichern
            $name = $roomForm->getElementByName('name')->getValue();
            $enabled = $roomForm->getElementByName('enabled')->getValue();
            $allowedUsers = $roomForm->getElementByName('allowedUsers')->getAllowedGroups();

            $message = new Message();
            try {

                RoomEditor::getInstance()->addRoom($name, $enabled, $allowedUsers);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.roomManagement.form.success.addRoom'));
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
            $tpl->assign('message', $message);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('addroomform.html');
    }

}