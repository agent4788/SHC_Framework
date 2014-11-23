<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use SHC\Core\SHC;
use SHC\Room\RoomEditor;
use SHC\View\Room\ViewHelperEditor;

/**
 * Startseite
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ShowRoomPage extends PageCommand {

    /**
     * Template
     *
     * @var String
     */
    protected $template = 'roomview.html';
    
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

        //Raum ID einlesen
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);

        //Template vorbereiten
        $tpl = RWF::getTemplate();
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('room', RoomEditor::getInstance()->getRoomById($roomId));
        $tpl->assign('viewHelper', ViewHelperEditor::getInstance()->getViewHelperForRoom($roomId));
    }

}
