<?php

namespace RWF\Request\Commands;

//Imports
use RWF\Request\Command;
use RWF\Request\Request;
use RWF\Request\Response;

/**
 * Action Anfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class ActionCommand implements Command {

    /**
     * Ziel nach dem ausfuehren
     * 
     * @var String
     */
    protected $location = '';
    
    /**
     * benoetigte Berechtigung
     * 
     * @var type 
     */
    protected $requiredPremission = '';
    
    /**
     * Anfrageobjekt
     * 
     * @var RWF\Request\Request
     */
    protected $request = null;
    
    /**
     * Anfrageobjekt
     * 
     * @var RWF\Request\Response
     */
    protected $response = null;
    
    /**
     * erzeugt die Seite
     * 
     * @param Request  $request  Anfrageobjekt
     * @param Response $response Antwortobjekt
     */
    public function execute(Request $request, Response $response) {
        
        $this->request = $request;
        $this->response = $response;
        
        //rechte Pruefen
        
        //Daten verarbeiten und in Antwortobjekt schreiben
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
