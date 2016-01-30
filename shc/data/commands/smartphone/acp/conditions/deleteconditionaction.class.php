<?php
namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\AbstractCondition;
use SHC\Condition\ConditionEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteConditionAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.conditionsManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=listconditions';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'conditionmanagement', 'switchablemanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Bedingung Objekt laden
        $conditionId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $condition = ConditionEditor::getInstance()->getConditionByID($conditionId);

        //pruefen ob das Element existiert
        if(!$condition instanceof AbstractCondition) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.id')));
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            ConditionEditor::getInstance()->removeCondition($conditionId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.delete.success'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.delete.error.1102'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.delete.error'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}