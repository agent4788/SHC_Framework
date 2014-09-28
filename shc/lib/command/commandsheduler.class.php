<?php

namespace SHC\Command;

//Imports
use SHC\SwitchServer\SwitchServerEditor;
use SHC\Command\Commands\RadioSocketCommand;
use SHC\Command\Commands\GpioOutputCommand;
use SHC\Command\Commands\GpioInputCommand;

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

        //Schaltserver abfragen
        $switchServers = SwitchServerEditor::getInstance()->listSwitchServers();

        //alle Server durchlaufen und die Daten senden
        foreach ($switchServers as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            //alle Kommandos durchlaufen und Daten Aufbereiten
            foreach ($this->commands as $command) {

                //Request Initialisieren
                $request = array();

                /* @var $command \SHC\Command\Command */
                if ($command instanceof RadioSocketCommand) {

                    //Funksteckdose
                    if ($switchServer->isRadioSocketsEnabled()) {

                        $request[] = $command->getCommandData();
                    }
                } elseif ($command instanceof GpioOutputCommand) {

                    //GPIO schalten
                    if ($switchServer->isWriteGpiosEnabled() && $switchServer->getId() == $command->getSwitchServer()) {

                        $request[] = $command->getCommandData();
                    }
                }
                
                //Kommando als aufgefuehrt markieren
                $command->executed();
            }

            //Daten zum versenden aufbereiten
            $data = json_encode($request);
            $data = base64_encode($data);

            //mit Schalserver verbinden und Daten senden
            $socket = $switchServer->getSocket();
            $socket->open();
            $socket->write($data);
            $socket->close();
        }
    }

    /**
     * seindet ein GPIO Lesekommando
     * 
     * @param  \SHC\Command\Commands\GpioInputCommand $command
     * @return Boolean
     */
    public function sendGPIOReadCommand(GpioInputCommand $command) {

        //Schaltserver abfragen
        $switchServers = SwitchServerEditor::getInstance()->listSwitchServers();

        //alle Server durchlaufen und die Daten senden
        foreach ($switchServers as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */
            if ($switchServer->isReadGpiosEnabled() && $switchServer->getId() == $command->getSwitchServer()) {

                //Request Initialisieren
                $request = array($command->getCommandData());
                
                //Daten zum versenden aufbereiten
                $data = json_encode($request);
                $data = base64_encode($data);

                //mit Schalserver verbinden und Daten senden
                $socket = $switchServer->getSocket();
                $socket->open();
                $socket->write($data);
                
                //Antwort Lesen
                $rawData = base64_decode($socket->read(8192));
                $response = json_decode($rawData, true);
                if(isset($response['state'])) {
                    
                    if((int) $response['state'] == GpioInputCommand::HIGH) {
                        
                        $command->setState(GpioInputCommand::HIGH);
                    } else {
                        
                        $command->setState(GpioInputCommand::LOW);
                    }
                }
                
                //Kommando als aufgefuehrt markieren
                $command->executed();
                
                //Verbindung trennen
                $socket->close();
                return true;
            }
        }
        return false;
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

            self::$instance = new CommandSheduler();
        }
        return self::$instance;
    }

}
