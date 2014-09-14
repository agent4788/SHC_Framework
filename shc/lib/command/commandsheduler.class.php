<?php

namespace SHC\Command;

//Imports


/**
 * Verwaltet und sendet die Kommandos an die Steckdosen/GPIOs
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class CommandSheduler {
    
    /**
     * liste mit allen Kommandos
     * 
     * @var Array 
     */
    protected $commands = array();
    
    /**
     * Singleton Instanz
     * 
     * @var \SHC\Command\CommandSheduler
     */
    protected static $instance = null;
    
    /**
     * fuegt ein neues Kommando hinzu
     * 
     * @param \SHC\Command\Command $command
     * @return \SHC\Command\CommandSheduler
     */
    public function addCommand(Command $command) {
        
        $this->commands[] = $command;
        return $this;
    }
    
    /**
     * entfernt ein Kommando
     * 
     * @param \SHC\Command\Command $command
     * @return \SHC\Command\CommandSheduler
     */
    public function removeCommand(Command $command) {
        
        $this->commands = array_diff($this->commands, array($command));
        return $this;
    }
    
    /**
     * entfernt alle Kommandos
     * 
     * @return \SHC\Command\CommandSheduler
     */
    public function removeAllCommands() {
        
        $this->commands = array();
        return $this;
    }
    
    /**
     * Sendet die Kommandos an den jeweiligen Schaltserver
     */
    public function sendCommands() {
        
    }
    
    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt den Raum Editor zurueck
     * 
     * @return \SHC\Command\CommandSheduler
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new RoomEditor();
        }
        return self::$instance;
    }
}
