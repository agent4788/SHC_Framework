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
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;


/**
 * loescht einen Raum
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteUserAtHomeAjax extends AjaxCommand {

    protected $premission = 'shc.acp.usersathomeManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usersathomemanagement');

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
            $this->data = $tpl->fetchString('deleteuserathome.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            UserAtHomeEditor::getInstance()->removeUserAtHome($userAtHomeId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.success.deleteUser'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deleteuserathome.html');
    }

}