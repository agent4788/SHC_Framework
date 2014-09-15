<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\CliCommand;
use RWF\IO\SocketServer;
use RWF\IO\Socket;

/**
 * Schaltserver
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServerCli extends CliCommand {

    /**
     * kurzer Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $shortParam = '-ss';

    /**
     * voller Kommandozeilen Parameter
     * 
     * @var String 
     */
    protected $fullParam = '--switchserver';

    /**
     * Debug Modus aktiv
     * 
     * @var Boolean 
     */
    protected $debug = false;

    /**
     * fuehrt das Kommando aus
     */
    public function executeCommand() {

        global $argv;

        //max_execution_time auser kraft setzen
        set_time_limit(0);
        //direkte ausgabe der Daten
        ob_implicit_flush();

        //Sprache einbinden
        RWF::getLanguage()->loadModul('SwitchServer');

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.switchServer.active')) {

            throw new Exception('Der Schaltserver wurde deaktiviert', 1600);
        }

        //Debug aktivieren
        if (in_array('-d', $argv) || in_array('--debug', $argv)) {

            $this->debug = true;
        }

        //Hilfe anzeigen
        if (in_array('-h', $argv) || in_array('--help', $argv)) {

            $this->writeHelp();
            return;
        }

        //Konfiguration
        if (in_array('-c', $argv) || in_array('--config', $argv)) {

            $this->config();
            return;
        }

        //Server Deamon stoppen
        if (in_array('-s', $argv) || in_array('--stop', $argv)) {

            $this->stop();
            return;
        }

        $this->executeCliCommand();
    }

    /**
     * gibt die Hilfe zu der Kommandozeilen Funktion auf die Kommandozeile aus
     */
    public function writeHelp() {
        
    }

    /**
     * konfiguriert das CLI Kommando
     */
    protected function config() {
        
    }

    /**
     * stoppt den Server
     */
    protected function stop() {

        $socket = new Socket(RWF::getSetting('shc.switchServer.ip'), RWF::getSetting('shc.switchServer.port'), 2);
        $socket->open();
        $socket->write(base64_encode(json_encode(array(array('stop' => 1)))));
        $socket->close();
    }

    /**
     * fuehrt das CLI Kommando aus
     */
    protected function executeCliCommand() {

        //Einstellungen laden
        $sendPath = RWF::getSetting('shc.switchServer.sendCommand');
        $gpioPath = RWF::getSetting('shc.switchServer.gpioCommand');
        $sendGpio = RWF::getSetting('shc.switchServer.sendLedPin');
        $senderActive = RWF::getSetting('shc.switchServer.senderActive');
        $writeGPIO = RWF::getSetting('shc.switchServer.writeGpio');
        $readGPIO = RWF::getSetting('shc.switchServer.readGpio');

        //GPIO's vorbereiten
        $gpioModeSet = array();

        //Server initialisieren
        $server = new SocketServer(RWF::getSetting('shc.switchServer.ip'), RWF::getSetting('shc.switchServer.port'));
        $server->startServer();

        //Startmeldung
        $this->response->writeLnColored(RWF::getLanguage()->get('switchServer.startedSuccessfully', RWF::getSetting('shc.switchServer.ip'), RWF::getSetting('shc.switchServer.port')), 'green');

        //GPIO fuer die Status LED initalisieren
        if ($sendGpio >= 0) {

            @shell_exec($gpioPath . ' mode ' . $sendGpio . ' out');
        }

        //auf Anfragen warten
        while (true) {

            //Anfragen vom Server holen
            $client = $server->accept();
            $rawData = base64_decode($client->read(8192));
            $requests = json_decode($rawData, true);

            //Status LED ein
            if ($sendGpio >= 0) {

                @shell_exec($gpioPath . ' write ' . $sendGpio . ' 1');
            }

            //Anfragen bearbeiten
            foreach ($requests as $request) {

                if ($senderActive && isset($request['type']) && $request['type'] = 'radiosocket') {

                    //Funksteckdosen schalten
                    @shell_exec('sudo ' . $sendPath . ' ' . $request['protocol'] . ' ' . $request['systemCode'] . ' ' . $request['deviceCode'] . ' ' . $request['command']);

                    //Debug ausgabe
                    if ($this->debug) {

                        $this->response->writeLnColored('sudo ' . $sendPath . ' ' . $request['protocol'] . ' ' . $request['systemCode'] . ' ' . $request['deviceCode'] . ' ' . $request['command'], 'light_blue');
                    }
                } elseif ($writeGPIO && isset($request['type']) && $request['type'] = 'gpiooutput') {

                    //GPIO schreiben
                    //Modus setzen
                    if (!in_array($gpio['pinNumber'], $gpioModeSet)) {

                        @shell_exec($gpioPath . ' mode ' . $request['pinNumber'] . ' out');
                        if ($this->debug) {

                            $this->response->writeLnColored($gpioPath . ' mode ' . $request['pinNumber'] . ' out', 'light_green');
                        }
                        $gpioModeSet[] = $gpio['pinNumber'];
                    }

                    //Befehl ausfuehren
                    @shell_exec($gpioPath . ' write ' . $request['pinNumber'] . ' ' . $request['command']);
                    if ($this->debug) {

                        $this->response->writeLnColored($gpioPath . ' write ' . $request['pinNumber'] . ' ' . $request['command'], 'light_green');
                    }
                } elseif ($readGPIO && isset($request['type']) && $request['type'] = 'gpioinput') {

                    //GPIO lesen
                    //noch nicht implementiert
                } elseif (isset($request['stop']) && $request['stop'] == 1) {

                    //Server Stoppen
                    $client->close();
                    $server->stopServer();
                    $this->response->writeLnColored(RWF::getLanguage()->get('switchServer.stoppedSuccessfully'), 'green');

                    //Status LED aus
                    if ($sendGpio >= 0) {

                        @shell_exec($gpioPath . ' write ' . $sendGpio . ' 0');
                    }
                    
                    return;
                }
            }

            //Status LED aus
            if ($sendGpio >= 0) {

                @shell_exec($gpioPath . ' write ' . $sendGpio . ' 0');
            }

            //bearbeitung abgeschlossen
            $client->close();
        }
    }

}
