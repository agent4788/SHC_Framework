<?php

namespace SHC\Command;

/**
 * Standard Schnittstelle fuer ein Kommando
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class AbstractCommand implements Command {
    
    /**
     * Kommando
     * 
     * @var Integer 
     */
    protected $command = null;
    
    /**
     * Kommando ausgefuehrt
     * 
     * @var Boolean 
     */
    protected $executed = false;

    /**
     * Antwortdaten
     * 
     * @var Array 
     */
    protected $response = array();

    /**
     * setzt das Kommando
     * 
     * @param  Integer $command Kommando
     * @return \SHC\Command\Command
     */
    public function setCommand($command) {
        
        $this->command = $command;
        return $this;
    }
    
    /**
     * gibt das Kommando zurueck
     * 
     * @return Integer
     */
    public function getCommand() {
        
        return $this->command;
    }
    
    /**
     * markiert das Kommando als ausgefuehrt
     */
    public function executed() {
        
        $this->executed = true;
    }
    
    /**
     * gibt an ob das Kommando bereits ausgefuehrt wurde
     * 
     * @return Boolean
     */
    public function isExecuted() {
        
        return $this->executed;
    }
    
    /**
     * setzt die Antwortdaten
     * 
     * @param Array $data Antwortdaten
     */
    public function setResponse(array $data) {
        
        $this->response = $data;
    }
    
    /**
     * gibt ein Array mit den Antwortdaten zurueck
     * 
     * @return Array
     */
    public function getResponse() {
        
        return $this->response;
    }
}
