<?php

namespace SHC\SwitchServer;

//Imports
use RWF\Core\RWF;
use RWF\IO\SocketServer;
use RWF\IO\SocketServerClient;
use RWF\Request\CliResponse;

/**
 * Schaltserver
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServerSocket {

    /**
     * GPIO Modus
     * 
     * @var Array 
     */
    protected $gpioModeSet = array();

    /**
     * Antwortobjekt
     * 
     * @var \RWF\Request\CliResponse
     */
    protected $response = null;

    /**
     * Debug Ausgabe
     * 
     * @var Boolean
     */
    protected $debug = false;

    /**
     * Server Objekt
     * 
     * @var RWF\IO\SocketServer 
     */
    protected $server = null;

    /**
     * startet den Server und wartet auf Anfragen
     * 
     * @param \RWF\Request\CliResponse $response Antwortobjekt
     * @param Boolean                  $debug    Debug Meldungen ausgeben
     * @return type
     */
    public function run(CliResponse $response, $debug = false) {

        $this->response = $response;
        $this->debug = $debug;

        //Einstellungen laden
        $gpioPath = RWF::getSetting('shc.switchServer.gpioCommand');
        $sendGpio = RWF::getSetting('shc.switchServer.sendLedPin');
        $senderActive = RWF::getSetting('shc.switchServer.senderActive');
        $writeGPIO = RWF::getSetting('shc.switchServer.writeGpio');
        $readGPIO = RWF::getSetting('shc.switchServer.readGpio');

        //Server initialisieren
        $this->server = new SocketServer(RWF::getSetting('shc.switchServer.ip'), RWF::getSetting('shc.switchServer.port'));
        $this->server->startServer();

        //Startmeldung
        $this->response->writeLnColored(RWF::getLanguage()->get('switchServer.startedSuccessfully', RWF::getSetting('shc.switchServer.ip'), RWF::getSetting('shc.switchServer.port')), 'green');

        //GPIO fuer die Status LED initalisieren
        if ($sendGpio >= 0) {

            @shell_exec($gpioPath . ' mode ' . $sendGpio . ' out');
        }

        //auf Anfragen warten
        while (true) {

            //Anfragen vom Server holen
            $client = $this->server->accept();
            $rawData = base64_decode($client->read(8192));
            $requests = json_decode($rawData, true);

            //Status LED ein
            if ($sendGpio >= 0) {

                @shell_exec($gpioPath . ' write ' . $sendGpio . ' 1');
            }

            //Anfragen bearbeiten
            foreach ($requests as $request) {

                if ($senderActive && isset($request['type']) && $request['type'] == 'radiosocket') {

                    //Funksteckdosen schalten
                    $this->send433MHzCommand($request);
                } elseif ($writeGPIO && isset($request['type']) && $request['type'] == 'gpiooutput') {

                    //GPIO schreiben
                    $this->writeGpio($request);
                } elseif ($readGPIO && isset($request['type']) && $request['type'] == 'gpioinput') {

                    //GPIO lesen
                    $this->readGpio($client, $request);
                } elseif (isset($request['stop']) && $request['stop'] == 1) {

                    //Server Stoppen
                    $client->close();
                    $this->stop();

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

    /**
     * sendet einen 433MHz Befehl
     * 
     * @param Array   $request Anfrage
     */
    protected function send433MHzCommand(array $request) {

        $sendPath = RWF::getSetting('shc.switchServer.sendCommand');
        @shell_exec('sudo ' . $sendPath . ' ' . $request['protocol'] . ' ' . $request['systemCode'] . ' ' . $request['deviceCode'] . ' ' . $request['command']);

        //Debug ausgabe
        if ($this->debug) {

            $this->response->writeLnColored('sudo ' . $sendPath . ' ' . $request['protocol'] . ' ' . $request['systemCode'] . ' ' . $request['deviceCode'] . ' ' . $request['command'], 'light_blue');
        }
    }

    /**
     * schreibt einen GPIO
     * 
     * @param Array   $request Anfrage
     */
    protected function writeGpio(array $request) {

        $gpioPath = RWF::getSetting('shc.switchServer.gpioCommand');
        
        //Modus setzen
        if (!in_array($gpio['pinNumber'], $this->gpioModeSet)) {

            @shell_exec($gpioPath . ' mode ' . $request['pinNumber'] . ' out');
            if ($this->debug) {

                $this->response->writeLnColored($gpioPath . ' mode ' . $request['pinNumber'] . ' out', 'light_green');
            }
            $this->gpioModeSet[] = $gpio['pinNumber'];
        }

        //Befehl ausfuehren
        @shell_exec($gpioPath . ' write ' . $request['pinNumber'] . ' ' . $request['command']);
        if ($this->debug) {

            $this->response->writeLnColored($gpioPath . ' write ' . $request['pinNumber'] . ' ' . $request['command'], 'light_green');
        }
    }

    /**
     * liest eine GPIO und sendet die Antwort an den Client
     * 
     * @param RWF\IO\SocketServerClient $client  Client Objekt
     * @param Array                     $request Anfrage
     */
    protected function readGpio(SocketServerClient $client, array $request) {

        $gpioPath = RWF::getSetting('shc.switchServer.gpioCommand');
        
        //Modus setzen
        if (!in_array($request['pinNumber'], $this->gpioModeSet)) {

            @shell_exec($gpioPath . ' mode ' . $request['pinNumber'] . ' in');
            $this->gpioModeSet[] = $request['pinNumber'];
            if ($this->debug) {

                $this->response->writeLnColored($gpioPath . ' mode ' . $request['pinNumber'] . ' in', 'light_green');
            }
        }

        //Befehl ausfuehren
        @exec($gpioPath . ' read ' . $request['pinNumber'], $data);

        //Daten Auswerten
        $data = trim($data[0]);
        $response = array();
        if ((int) $data == 1) {

            $response['state'] = 1;
        } else {

            $response['state'] = 0;
        }

        //Antwort an Client schicken
        $responseData = json_encode($response);
        $responseData = base64_encode($responseData);
        $client->write($responseData);
        $client->close();

        //Debug ausgabe
        if ($this->debug) {

            $this->response->writeLnColored($gpioPath . ' read ' . $request['pinNumber'], 'light_green');
            $this->response->writeLnColored($data, 'light_green');
        }
    }

    /**
     * beendet den Server
     */
    protected function stop() {

        $this->server->stopServer();
        $this->response->writeLnColored(RWF::getLanguage()->get('switchServer.stoppedSuccessfully'), 'green');
    }

}
