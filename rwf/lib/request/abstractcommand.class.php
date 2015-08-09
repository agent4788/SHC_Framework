<?php

namespace RWF\Request;

//Imports
use RWF\Core\RWF;
use RWF\Exception\AccessDeniedException;

/**
 * Basisklasse fuer die Kommandos
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractCommand implements Command {
    
    /**
     * benoetigte Berechtigung
     * 
     * @var string
     */
    protected $requiredPremission = '';
    
    /**
     * Sprachpakete die geladen werden sollen
     * 
     * @var Array 
     */
    protected $languageModules = array();
    
    /**
     * Anfrageobjekt
     * 
     * @var \RWF\Request\Request
     */
    protected $request = null;
    
    /**
     * Anfrageobjekt
     * 
     * @var \RWF\Request\CliResponse
     */
    protected $response = null;
    
    /**
     * erzeugt die Seite
     * 
     * @param Request  $request  Anfrageobjekt
     * @param Response $response Antwortobjekt
     * @throws \RWF\Exception\AccessDeniedException
     */
    public function execute(Request $request, Response $response) {
        
        //Request und Respons speichern
        $this->request = $request;
        $this->response = $response;
        
        //rechte Pruefen
        if($this->requiredPremission != '' && RWF::getVisitor()->checkPermission($this->requiredPremission) === false) {
            
            throw new AccessDeniedException($this->requiredPremission);
        }
        
        //Sprachmodule laden
        if(count($this->languageModules) > 0) {
            
            foreach($this->languageModules as $modul) {
                
                RWF::getLanguage()->loadModul($modul);
            }
        }
        
        //Kommando ausfuehren
        $this->executeCommand();
    }
    
    /**
     * fuehrt das Kommando aus
     */
    abstract protected function executeCommand();
}
