<?php

namespace SHC\Command;

/**
 * Schnittstelle fuer ein Kommando
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Command {
    
    /**
     * Befehl ein schalten
     * 
     * @var Integer
     */
    const SWITCH_ON = 1;
    
    /**
     * Befehl aus schalten
     * 
     * @var Integer
     */
    const SWITCH_OFF = 0;
    
    /**
     * setzt das Kommando
     * 
     * @param  Integer $command Kommando
     * @return \SHC\Command\Command
     */
    public function setCommand($command);
    
    /**
     * gibt das Kommando zurueck
     * 
     * @return Integer
     */
    public function getCommand();
    
    /**
     * gibt ein Array mit den zu sendenen Daten zurueck
     * 
     * @return Array
     */
    public function getCommandData();
    
    /**
     * markiert das Kommando als ausgefuehrt
     */
    public function executed();
    
    /**
     * gibt an ob das Kommando bereits ausgefuehrt wurde
     * 
     * @return Boolean
     */
    public function isExecuted();
}
