<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\Condition;
use SHC\Condition\ConditionEditor;
use SHC\Timer\SwitchPoint;
use SHC\Timer\SwitchPointEditor;

/**
 * Bedingung zu Ereignis Hinzufügen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddConditionToSwitchPointAction extends ActionCommand {

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
    protected $location = 'index.php?app=shc&page=manageswitchablesinswitchpoints&id=';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchpointsmanagment');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Schaltpunkt Objekt laden
        $switchPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);

        if ($switchPoint instanceof SwitchPoint) {

            $this->location .= $switchPointId;

            //Bedingung Objekt laden
            $conditionId = RWF::getRequest()->getParam('condition', Request::GET, DataTypeUtil::INTEGER);
            $condition = ConditionEditor::getInstance()->getConditionByID($conditionId);

            //Eingaben pruefen
            $error = false;
            $message = new Message();
            if (!$condition instanceof Condition) {

                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id'));
                $error = true;

            }

            //Element hinzufuegen
            if ($error === false) {

                try {

                    //Speichern
                    SwitchPointEditor::getInstance()->addConditionToSwitchPoint($switchPointId, $conditionId);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.addCondition.success'));
                } catch (\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.addCondition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.addCondition.error'));
                    }
                }
            }
        } else {

            $this->location = 'index.php?app=shc&page=listswitchables';
            $message = new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id'));
        }
        RWF::getSession()->setMessage($message);
    }
}