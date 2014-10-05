<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use SHC\View\Room\ViewHelperEditor;

/**
 * Raum Anzeigen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ShowRoomAjax extends AjaxCommand {
    
    /**
     * Sprachpakete die geladen werden sollen
     * 
     * @var Array 
     */
    protected $languageModules = array('room');
    
    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Raum ID einlesen
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        
        $this->data = ViewHelperEditor::getInstance()->getViewHelperForRoom($roomId)->showAll();
    }

}
