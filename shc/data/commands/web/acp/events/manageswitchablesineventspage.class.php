<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Condition\ConditionEditor;
use SHC\Core\SHC;
use SHC\Event\EventEditor;
use RWF\Form\FormElements\Select;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Event\Event;
use SHC\Form\FormElements\SwitchCommandChooser;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * Zeigt eine Liste mit allen Schaltservern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ManageSwitchablesInEventsPage extends PageCommand {

    protected $template = 'manageswitchablesinevents.html';

    protected $requiredPremission = 'shc.acp.eventsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'eventmanagement', 'conditionmanagement', 'acpindex', 'form', 'index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //Meldungen
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        //Ereignis Objekt laden
        $eventId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $event = EventEditor::getInstance()->getEventById($eventId);

        if($event instanceof Event) {

            //Formularfelder erstellen

            //Bedingungen
            $conditionChooser = new Select('condition');
            $values = array();
            foreach(ConditionEditor::getInstance()->listConditions(ConditionEditor::SORT_BY_NAME) as $condition) {

                /* @var $condition \SHC\Condition\Condition */
                //pruefen ob Bedingung schon registriert
                $found = false;
                foreach($event->listConditions() as $compareCondition) {

                    if($compareCondition == $condition) {

                        $found = true;
                        break;
                    }
                }
                if($found == true) {

                    //wenn schon registriert Bedingung ueberspringen
                    continue;
                }

                RWF::getLanguage()->disableAutoHtmlEndocde();
                $type = $condition->getTypeName();
                RWF::getLanguage()->enableAutoHtmlEndocde();
                $values[$condition->getId()] = $condition->getName() .' ('. $type .')';
            }
            $conditionChooser->setValues($values);

            //schaltbare Elemente
            $elementChooser = new Select('element');
            $values = array();
            foreach(SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME) as $switchableElement) {

                if($switchableElement instanceof Switchable) {

                    //pruefen ob Element schon registriert
                    $found = false;
                    foreach($event->listSwitchables() as $switchable) {

                        if($switchable['object'] == $switchableElement) {

                            $found = true;
                            break;
                        }
                    }
                    if($found == true) {

                        //wenn schon registriert Element ueberspringen
                        continue;
                    }

                    RWF::getLanguage()->disableAutoHtmlEndocde();
                    $type = $switchableElement->getTypeName();
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    $values[$switchableElement->getId()] = $switchableElement->getName() .' ('. $type .') ['. $switchableElement->getNamedRoomList(true) .']';
                }
            }
            $elementChooser->setValues($values);

            //Schaltbefehl
            $switchCommand = new SwitchCommandChooser('switchCommand');

            //Elemente Liste Template Anzeigen
            $tpl->assign('event', $event);
            $tpl->assign('conditionChooser', $conditionChooser);
            $tpl->assign('elementChooser', $elementChooser);
            $tpl->assign('switchCommand', $switchCommand);
            $tpl->assign('elementList', $event->listSwitchables());
            $tpl->assign('conditionList', $event->listConditions());
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.eventsManagement.form.error.id')));
            return;
        }
    }

}