<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Event\Event;
use SHC\Event\EventEditor;
use SHC\Switchable\AbstractSwitchable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * schaltbares Element zu Ereignis Hinzufügen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ToggleSwitchableCommandAction extends ActionCommand {

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

            //element hinzufuegen
            $switchableElementId = RWF::getRequest()->getParam('element', Request::GET, DataTypeUtil::INTEGER);

            //Eingaben pruefen
            $error = false;
            $message = new Message();
            $switchableElementObject = SwitchableEditor::getInstance()->getElementById($switchableElementId);
            if (!$switchableElementObject instanceof Switchable) {

                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.error.id'));
                $error = true;
            }

            //Element hinzufuegen
            if ($error === false) {

                try {

                    //Speichern
                    $newCommand = AbstractSwitchable::STATE_OFF;
                    foreach($event->listSwitchables() as $switchable) {
                        if($switchable['object'] == $switchableElementObject) {
                            if($switchable['command'] == AbstractSwitchable::STATE_ON) {
                                $newCommand = AbstractSwitchable::STATE_OFF;
                            } else {
                                $newCommand = AbstractSwitchable::STATE_ON;
                            }
                        }
                    }
                    EventEditor::getInstance()->setEventSwitchableCommand($eventId, $switchableElementId, $newCommand);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.addElement.success'));
                } catch (\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.addElement.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.addElement.error'));
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