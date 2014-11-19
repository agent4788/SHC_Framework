<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\User\User;
use RWF\User\UserEditor;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;


/**
 * loescht einen Benutzer
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteUserAjax extends AjaxCommand {

    protected $premission = 'shc.acp.userManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('usermanagement');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Benutzer Objekt laden
        $userId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $user = UserEditor::getInstance()->getUserById($userId);

        //pruefen ob der Benutzer existiert
        if(!$user instanceof User) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.userManagement.form.error.id')));
            $this->data = $tpl->fetchString('deleteuser.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            UserEditor::getInstance()->removeUser($userId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.success.deleteUser'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1102.del'));
            } elseif($e->getCode() == 1111) {

                //Hauptbenutzer
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.1111.del'));
            }else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.userManagement.form.error.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deleteuser.html');
    }

}