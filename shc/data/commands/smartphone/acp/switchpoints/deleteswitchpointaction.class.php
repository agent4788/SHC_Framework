<?php
namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Timer\SwitchPoint;
use SHC\Timer\SwitchPointEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteSwitchPointAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.switchpointsManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=listswitchpoints';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchpointsmanagment', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Schaltpunkt Objekt laden
        $switchPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);

        //pruefen ob der Schaltpunkt existiert
        if (!$switchPoint instanceof SwitchPoint) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id')));
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            SwitchPointEditor::getInstance()->removeSwitchPoint($switchPointId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.success.delete'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.error.del'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}