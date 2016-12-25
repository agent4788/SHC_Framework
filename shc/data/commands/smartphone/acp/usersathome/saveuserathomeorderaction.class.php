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
 * Speichert die Sortierung der Benutzer zu Hause
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SaveUserAtHomeOrderAction extends ActionCommand {

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
    protected $languageModules = array('usersathomemanagement', 'form');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        $r = $this->request;

        //Meldung
        $message = new Message();

        //eingabedaten Pruefen
        $order = $r->getParam('userOrder', Request::POST);
        $filteredOrder = array();
        $valid = true;
        foreach($order as $id => $orderId) {

            $userAtHome = UserAtHomeEditor::getInstance()->getUserTaHomeById($id);
            if($userAtHome instanceof UserAtHome) {

                $filteredOrder[$id] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
            } else {

                //Fehlerhafte Eingaben
                $valid = false;
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                break;
            }
        }

        //Speichern
        if($valid === true) {

            try {

                UserAtHomeEditor::getInstance()->editOrder($filteredOrder);
                UserAtHomeEditor::getInstance()->loadData();
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.success.orderUser'));
            } catch (\Exception $e) {

                //Fehler beim speichern
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.usersathomeManagement.form.error.orderUser'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}