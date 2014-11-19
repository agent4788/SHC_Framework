<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\AbstractCondition;
use SHC\Condition\ConditionEditor;
use SHC\Event\Event;
use SHC\Event\EventEditor;

/**
 * loescht ein Ereignis
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteEventAjax extends AjaxCommand {

    protected $premission = 'shc.acp.eventsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('eventmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Ereignis Objekt laden
        $eventId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $event = EventEditor::getInstance()->getEventById($eventId);

        //Objekt pruefen
        if(!$event instanceof Event) {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.eventsManagement.form.error.id')));
            $this->data = $tpl->fetchString('deleteevent.html');
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
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deleteevent.html');
    }

}