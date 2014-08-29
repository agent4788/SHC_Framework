<?php

namespace RWF\Request\Commands;

//Imports
use RWF\Request\AbstractCommand;

/**
 * Seiten Anfrage
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class PageCommand extends AbstractCommand {

    /**
     * Template
     * 
     * @var String
     */
    protected $template = '';
    
    /**
     * fuehrt das Kommando aus
     */
    protected function executeCommand() {
        
        $this->processData();
        $this->fetch();
    }
    
    /**
     * Daten verarbeiten
     */
    public abstract function processData();
    
    /**
     * Seite in das Antwortobjekt schreiben
     */
    public function fetch() {
        
        //wenn Template angegeben Template verarbeiten
    }
}
