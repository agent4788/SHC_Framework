<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\RoomForm;
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
class AddRoomFormPage extends PageCommand {

    protected $template = 'addroomform.html';

    protected $premission = 'shc.acp.roomManagement';

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

        //Formular erstellen
        $roomForm = new RoomForm();
        $roomForm->setView(RoomForm::SMARTPHONE_VIEW);
        $roomForm->setAction('index.php?app=shc&m&page=addroomform');
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
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listrooms');
            $this->response->setBody('');
            $this->template = '';
        }

    }
}