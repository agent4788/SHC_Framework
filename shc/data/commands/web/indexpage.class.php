<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\PageCommand;
use SHC\Core\SHC;
use SHC\Room\RoomEditor;

/**
 * Startseite
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class IndexPage extends PageCommand {
    
    /**
     * Template
     * 
     * @var String
     */
    protected $template = 'indexpage.html';
    
    /**
     * Sprachpakete die geladen werden sollen
     * 
     * @var Array 
     */
    protected $languageModules = array('index', 'room');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        SHC::getTemplate()->assign('style', SHC::getStyle());
        SHC::getTemplate()->assign('user', SHC::getVisitor());
        SHC::getTemplate()->assign('roomList', RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID));
    }
}
