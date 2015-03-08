<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Form\Forms\RoomForm;
use SHC\Form\Forms\UserAtHomeForm;
use SHC\Room\Room;
use SHC\Room\RoomEditor;
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * bearbeitet einen benutzer zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditUserAtHomeFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.usersathomeManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usersathomemanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Benutzer Objekt laden
        $userAtHomeId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $userAtHome = UserAtHomeEditor::getInstance()->getUserTaHomeById($userAtHomeId);

        //pruefen ob der Benutzer existiert
        if(!$userAtHome instanceof UserAtHome) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.usersathomeManagement.form.error.id')));
            $this->data = $tpl->fetchString('edituserathomeform.html');
            return;
        }

        //Formular erstellen
        $userAtHomeForm = new UserAtHomeForm($userAtHome);
        $userAtHomeForm->addId('shc-view-form-editUserAtHome');

        if($userAtHomeForm->isSubmitted() && $userAtHomeForm->validate() === true) {

            //Speichern
            $name = $userAtHomeForm->getElementByName('name')->getValue();
            $ip = $userAtHomeForm->getElementByName('ip')->getValue();
            $visibility = $userAtHomeForm->getElementByName('visibility')->getValue();
            $enabled = $userAtHomeForm->getElementByName('enabled')->getValue();

            $message = new Message();
            try {

                UserAtHomeEditor::getInstance()->editUserAtHome($userAtHomeId, $name, $ip, $enabled, $visibility);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.success.editUser'));
            } catch(\Exception $e) {

                if($e->getCode() == 1507) {

                    //Raumname schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.1507'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error'));
                }
            }
            $tpl->assign('message', $message);
        } else {

            $tpl->assign('userAtHome', $userAtHome);
            $tpl->assign('userAtHomeForm', $userAtHomeForm);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('edituserathomeform.html');
    }

}