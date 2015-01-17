<?php
namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Event\Event;
use SHC\Event\EventEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteEventAction extends ActionCommand {

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
    protected $location = 'index.php?app=shc&m&page=listevents';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'eventmanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //Ereignis Objekt laden
        $eventId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $event = EventEditor::getInstance()->getEventById($eventId);

        //Objekt pruefen
        if(!$event instanceof Event) {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.eventsManagement.form.error.id')));
            return;
        }

        //Ereignis loeschen
        $message = new Message();
        try {

            EventEditor::getInstance()->removeEvent($eventId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.delete.success'));
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.delete.error.1102'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.delete.error'));
            }
        }
        RWF::getSession()->setMessage($message);
    }
}