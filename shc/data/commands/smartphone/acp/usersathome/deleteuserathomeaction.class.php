<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\UserAtHome\UserAtHome;
use SHC\UserAtHome\UserAtHomeEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteUserAtHomeAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.usersathomeManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=listusersathome';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'usersathomemanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Benutzer Objekt laden
        $userAtHomeId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $userAtHome = UserAtHomeEditor::getInstance()->getUserTaHomeById($userAtHomeId);

        //pruefen ob der Benutzer existiert
        if(!$userAtHome instanceof UserAtHome) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.usersathomeManagement.form.error.id')));
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
        RWF::getSession()->setMessage($message);
    }
}