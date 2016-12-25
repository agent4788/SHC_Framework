<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Event\EventEditor;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListSwitchFunctionsPage extends PageCommand {

    protected $template = 'listswitchfunctions.html';

    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchablemanagement', 'eventmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listswitchables');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Element Objekt laden
        $elementId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchable = SwitchableEditor::getInstance()->getElementById($elementId);

        if($switchable instanceof Switchable) {

            //element
            $tpl->assign('switchable', $switchable);

            //Schaltpunkte
            $switchPoints = $switchable->listSwitchPoints();
            $tpl->assign('switchPoints', $switchPoints);

            //Ereignisse
            $eventList = array();
            $events = EventEditor::getInstance()->listEvents();
            foreach($events as $event) {

                /* @var $event \SHC\Event\Event */
                $eventSwitchables = $event->listSwitchables();
                foreach($eventSwitchables as $eventSwitchable) {

                    /* @var $eventSwitchable \SHC\Switchable\Switchable */
                    if($eventSwitchable['object']->getId() == $switchable->getId()) {

                        $eventList[] = $event;
                    }
                }
            }
            $tpl->assign('eventList', $eventList);

        } else {

            $message = new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.prompt.error.notSwitchable'));
            $tpl->assign('message', $message);
        }
    }

}