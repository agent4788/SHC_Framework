<?php
namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\SwitchServer\SwitchServer;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteSwitchServerAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.switchserverManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=listswitchservers';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchservermanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Schaltserver Objekt laden
        $switchServerId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchServer = SwitchServerEditor::getInstance()->getSwitchServerById($switchServerId);

        //pruefen ob der Benutzer existiert
        if(!$switchServer instanceof SwitchServer) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchserverManagement.form.error.id')));
            return;
        }

        //Schaltserver loeschen
        $message = new Message();
        try {

            SwitchServerEditor::getInstance()->removeSwitchServer($switchServerId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.success.deleteSwitchServer'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchserverManagement.form.error.del'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}