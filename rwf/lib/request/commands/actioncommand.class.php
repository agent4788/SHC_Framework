<?php

namespace RWF\Request\Commands;

//Imports
use RWF\Request\AbstractCommand;

/**
 * Action Anfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class ActionCommand extends AbstractCommand {

    /**
     * Ziel nach dem ausfuehren
     * 
     * @var String
     */
    protected $location = '';
    
    /**
     * fuehrt das Kommando aus
     */
    protected function executeCommand() {
        
        $this->executeAction();
        $this->executedAction();
    }
    
    /**
     * Aktion ausfuehren
     */
    public abstract function executeAction();
    
    /**
     * aktion ausgefuehrt
     */
    public function executedAction() {
        
        if(!empty($this->location)) {
            
            $this->response->addLocationHeader($this->location);
            $this->response->setBody('');
        }
    }

}
