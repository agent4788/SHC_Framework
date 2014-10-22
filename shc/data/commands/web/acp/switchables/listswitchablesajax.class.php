<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Room\RoomEditor;
use SHC\View\Room\ViewHelperEditor;


/**
 * Zeigt eine Liste mit allen Elementen an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListSwitchablesAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        //Template vorbereiten und Anzeigen
        $tpl = RWF::getTemplate();
        $tpl->assign('roomList', RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID));
        $tpl->assign('viewHelperEditor', ViewHelperEditor::getInstance());
        $this->data = $tpl->fetchString('listswitchables.html');
    }

}