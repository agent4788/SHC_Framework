<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\User\User;
use RWF\User\UserEditor;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteUserAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'pcc.acp.userManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=pcc&page=listusers';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usermanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Benutzer Objekt laden
        $userId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $user = UserEditor::getInstance()->getUserById($userId);

        //pruefen ob der Benutzer existiert
        if(!$user instanceof User) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.userManagement.form.error.id')));
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
        RWF::getSession()->setMessage($message);
    }
}