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
use SHC\Event\Event;
use SHC\Event\EventEditor;

/**
 * Bedingung aus Ereignis loeschen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteConditionFromEventAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.eventsManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&page=manageswitchablesinevents&id=';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('eventmanagement');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Ereignis Objekt laden
        $eventId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $event = EventEditor::getInstance()->getEventById($eventId);

        if($event instanceof Event) {

            $this->location .= $eventId;

            //Bedingung Objekt laden
            $conditionId = RWF::getRequest()->getParam('condition', Request::GET, DataTypeUtil::INTEGER);
            $condition = ConditionEditor::getInstance()->getConditionByID($conditionId);

            //Eingaben pruefen
            $error = false;
            $message = new Message();
            if (!$condition instanceof Condition) {

                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.error.id'));
                $error = true;

            }

            //Element hinzufuegen
            if ($error === false) {

                try {

                    //loeschen
                    EventEditor::getInstance()->removeConditionFromEvent($eventId, $conditionId);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.addCondition.removeSuccesss'));
                    EventEditor::getInstance()->loadData();
                } catch (\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.addCondition.removeError.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.addCondition.removeError'));
                    }
                }
            }
        } else {

            $this->location = 'index.php?app=shc&page=listevents';
            $message = new Message(Message::ERROR, RWF::getLanguage()->get('acp.eventsManagement.form.error.id'));
        }
        RWF::getSession()->setMessage($message);
    }
}