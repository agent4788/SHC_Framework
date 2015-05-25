<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Core\SHC;
use SHC\Room\RoomEditor;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListRoomsPage extends PageCommand {

    protected $premission = 'shc.acp.roomManagement';

    protected $template = 'listrooms.html';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'roommanagement', 'acpindex', 'form');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
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

        //Raeume auflisten
        $tpl->assign('roomList', RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID));
    }

}