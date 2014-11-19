<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Event\EventEditor;

/**
 * Zeigt eine Liste mit allen Ereignissen an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListEventsAjax extends AjaxCommand {

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

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Ereignisse auflisten
        $tpl->assign('eventList', EventEditor::getInstance()->listEvents(EventEditor::SORT_BY_NAME));
        $this->data = $tpl->fetchString('listevents.html');
    }

}