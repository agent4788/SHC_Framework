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
     *
     * @throws \Exception
     */
    public function sendCommands() {

        //Schaltserver abfragen
        $switchServers = SwitchServerEditor::getInstance()->listSwitchServers();

        //alle Server durchlaufen und die Daten senden
        $radioSocketsCount = 0;
        foreach ($switchServers as $switchServer) {

            /* @var $switchServer \SHC\SwitchServer\SwitchServer */

            //Hilfsvariablen vorbereiten
            $radioSocketsActive = false;
            $radioSocketsSend = false;
            $gpioActive = false;
            $gpioSend = false;

            //Request Initialisieren
            $request = array();

            //alle Kommandos durchlaufen und Daten Aufbereiten
            foreach ($this->commands as $command) {

                /* @var $command \SHC\Command\Command */
                if ($command instanceof RadioSocketCommand) {

                    //Funksteckdose
                    $radioSocketsActive = true;
                    if ($switchServer->isRadioSocketsEnabled()) {

                        $request[] = $command->getCommandData();
                        $radioSocketsSend = true;
                    }
                } elseif ($command instanceof GpioOutputCommand) {

                    //GPIO schalten
                    $gpioActive = true;
                    if ($switchServer->isWriteGpiosEnabled() && $switchServer->getId() == $command->getSwitchServer()) {

                        $request[] = $command->getCommandData();
                        $gpioSend = true;
                    }
                }
                
                //Kommando als aufgefuehrt markieren
                $command->executed();
            }

            //Kommandos loeschen
            $this->commands = array();

            //Daten zum versenden aufbereiten
            $data = json_encode($request);
            $data = base64_encode($data);

            try {

                //mit Schalserver verbinden und Daten senden
                $socket = $switchServer->getSocket();
                $socket->open();
                $socket->write($data);
                $socket->close();
                //hochzaehlen wenn Funksteckdosen geschalten werden konnten
                if($radioSocketsActive === true && $radioSocketsSend === true) {

                    $radioSocketsCount++;
                }
            } catch(\Exception $e) {

                if($gpioActive === true && $gpioSend === true) {

                    //GPIO Schaltserver nicht errreicht
                    throw new \Exception('der Schaltserver für den GPIO konnte nicht erreicht werden', 1510);
                } elseif($gpioActive === true && $gpioSend === true) {

                    //Schaltserver unterstuetzt kein GPIO schalten
                    throw new \Exception('der Schaltserver untersützt das GPIO schalten nicht', 1511);
                }
            }
        }

        //kein Schaltserver erreichbar
        if($radioSocketsActive === true && $radioSocketsCount == 0) {

            throw new \Exception('es konnte kein Schaltserver erreicht werden oder kein Schaltserver untersützt das schalten von Funksteckosen', 1512);
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

                try {

                    //mit Schalserver verbinden und Daten senden
                    $socket = $switchServer->getSocket();
                    $socket->open();
                    $socket->write($data);
                } catch(\Exception $e) {

                    //Verbindungsfehler, abbruch
                    return false;
                }
                
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
