<?php

namespace SHC\SwitchServer;

//Imports
use RWF\Core\RWF;
use RWF\IO\SocketServer;
use RWF\IO\SocketServerClient;
use RWF\Request\CliResponse;
use RWF\Util\FileUtil;
use SHC\Command\CLI\SwitchServerCli;

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
     * @var \RWF\IO\SocketServer
     */
    protected $server = null;

    /**
     * Liste mit den Protokollen welche eine ID benoetigen Statt einem Systemcode
     *
     * @var Array
     */
    protected $protocolsWithId = array(
        'clarus_switch',
        'cogex',
        'kaku_switch_old',
        'intertechno_old',
        'duwi',
        'cleverwatts',
        'coco_switch',
        'dio_switch',
        'intertechno_switch',
        'kaku_switch',
        'nexa_switch',
        'quigg_switch',
        'silvercrest',
        'unitech',
        'rev_v1',
        'rev_v2',
        'rev_v3',
        'rev1_switch',
        'rev2_switch',
        'rev3_switch',
        'techlico_switch',
        'X10',
        'eHome',
        'beamish_switch',
        'rc101',
        'rc102',
        'techlico_switch'
    );

    /**
     * CLI Kommando
     *
     * @var \SHC\Command\CLI\SwitchServerCli
     */
    protected $command = null;

    /**
     * @param SwitchServerCli $command
     */
    public function __construct(SwitchServerCli $command) {

        $this->command = $command;
    }

    /**
     * startet den Server und wartet auf Anfragen
     * 
     * @param \RWF\Request\CliResponse $response Antwortobjekt
     * @param Boolean                  $debug    Debug Meldungen ausgeben
     */
    public function run(CliResponse $response, $debug = false) {

        $this->response = $response;
        $this->debug = $debug;

        //Einstellungen laden
        $gpioPath = $this->command->getSetting('shc.switchServer.gpioCommand');
        $sendGpio = $this->command->getSetting('shc.switchServer.sendLedPin');
        $senderActive = $this->command->getSetting('shc.switchServer.senderActive');
        $writeGPIO = $this->command->getSetting('shc.switchServer.writeGpio');
        $readGPIO = $this->command->getSetting('shc.switchServer.readGpio');

        //Server initialisieren
        $this->server = new SocketServer($this->command->getSetting('shc.switchServer.ip'), $this->command->getSetting('shc.switchServer.port'));
        $this->server->startServer();

        //Startmeldung
        $this->response->writeLnColored(RWF::getLanguage()->get('switchServer.startedSuccessfully', $this->command->getSetting('shc.switchServer.ip'), $this->command->getSetting('shc.switchServer.port')), 'green');

        //GPIO fuer die Status LED initalisieren
        if ($sendGpio >= 0) {

            @shell_exec($gpioPath . ' mode ' . $sendGpio . ' out');
        }

        //auf Anfragen warten
        while (true) {

            //Run Flag alle 60 Sekunden setzen
            $dateTime = new \DateTime('now');
            if(!isset($runTime)) {

                $runTime = $dateTime;
            }
            if($runTime <= $dateTime) {

                if(!file_exists(PATH_RWF_CACHE . 'switchServer.flag')) {

                    FileUtil::createFile(PATH_RWF_CACHE . 'switchServer.flag', 0777, true);
                }
                file_put_contents(PATH_RWF_CACHE . 'switchServer.flag', $dateTime->format('Y-m-d H:i:s'));
                $runTime->add(new \DateInterval('PT1M'));
            }

            //Anfragen vom Server holen
            $client = $this->server->accept();
            $rawData = base64_decode($client->read(8192));
            $requests = json_decode($rawData, true);

            //leere Anfragen ignorieren
            if(count($requests)) {

                //Status LED ein
                if ($sendGpio >= 0) {

                    @shell_exec($gpioPath . ' write ' . $sendGpio . ' 1');
                }

                //Anfragen bearbeiten
                $sendRequests = array();
                foreach ($requests as $request) {

                    if ($senderActive && isset($request['type']) && $request['type'] == 'radiosocket') {

                        //Funksteckdosen schalten
                        $sendRequests[] = $request;
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

                //433MHz Signale senden
                if(count($sendRequests) > 0) {


                    $this->send433MHzCommand($sendRequests);
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

    /**
     * sendet einen 433MHz Befehl
     * 
     * @param Array $request Liste mit allen Anfragen
     */
    protected function send433MHzCommand(array $requests) {

        $sendPath = $this->command->getSetting('shc.switchServer.sendCommand');
        $rcSendPath = $this->command->getSetting('shc.switchServer.rcswitchPiCommand');

        //Anfragen solange durchlaufen bis auch alle mehrfach gesendeten Befehle versndet sind
        $requestData = array();
        $firstRun = true;
        while(true) {

            //pruefen ob alle Anfragen abgearbeitet
            if($firstRun === false) {

                $successfull = true;
                foreach ($requestData as $reqData) {

                    if ($reqData['continuous'] > 0) {

                        $successfull = false;
                    }
                }
                if ($successfull === true) {

                    //alle Befehle gesendet
                    return;
                }
            }

            //Alle Anfragen durchlaufen
            for($i = 0; $i < count($requests); $i++) {

                //geloeschten Index ueberspringen
                if(!isset($requests[$i])) {

                    continue;
                }
                $request = $requests[$i];
                $firstRun = false;

                //Pruefen ob weitere Sendevorgaenge anstehen
                if ((isset($requestData[$i]['continuous']) && $requestData[$i]['continuous'] > 0) || !isset($requestData[$i]['continuous'])) {

                    //Wartezeit falls notwendig
                    if(isset($requestData[$i]['time'])) {

                        $timeDiff = microtime(true) - $requestData[$i]['time'];
                        if($timeDiff < 1000) {

                            //min 1s Wartezeit zwischen 2 sende Befehlen
                            usleep((1000 - $timeDiff) * 1000);
                        }
                    } else {

                        //Erster durchlauf -> Daten anlegen
                        $requestData[$i]['continuous'] = $request['continuous'];
                        $requestData[$i]['time'] = 0.0;
                    }

                    if ($request['protocol'] == 'elro_rc') {

                        if(!preg_match('#^[01]{5}$#', $request['systemCode']) || $request['deviceCode'] < 0 || $request['deviceCode'] > 5) {

                            $this->response->writeLnColored('fehlerhafte Argumente für rcswitch-pi', 'red');
                            continue;
                        }

                        shell_exec('sudo ' . $rcSendPath . ' ' . $request['systemCode'] . ' ' . $request['deviceCode'] . ' ' . ($request['command'] == 1 ? '1' : '0'));
                        //Debug ausgabe
                        if ($this->debug) {

                            $this->response->writeLnColored('sudo '  . $rcSendPath  . ' ' . $request['systemCode'] . ' ' . $request['deviceCode'] . ' ' . ($request['command'] == 1 ? '1' : '0'), 'light_blue');
                        }
                    } else {

                        //Binaerzahl in Dezimalzahl unrechnen
                        if(preg_match('#^[01]{5}$#', $request['systemCode'])) {

                            $systemCode = 0;
                            for($j = 0; $j < strlen($request['systemCode']); $j++) {

                                $bin_tmp = substr($request['systemCode'], $j, 1);
                                $systemCode += $bin_tmp * (pow(2, $j));
                            }
                        } else {

                            $systemCode = $request['systemCode'];
                        }

                        usleep(50000); //50ms vor dem Schaltbefehl warten

                        //Sende Befehl ausfuehren
                        if(in_array($request['protocol'], $this->protocolsWithId)) {

                            shell_exec('sudo ' . $sendPath . ' -p ' . escapeshellarg($request['protocol']) . ' --id=' . escapeshellarg($systemCode) . ' --unit=' . escapeshellarg($request['deviceCode']) . ' ' . ($request['command'] == 1 ? '--on' : '--off'));
                        } else {

                            shell_exec('sudo ' . $sendPath . ' -p ' . escapeshellarg($request['protocol']) . ' --systemcode=' . escapeshellarg($systemCode) . ' --unitcode=' . escapeshellarg($request['deviceCode']) . ' ' . ($request['command'] == 1 ? '--on' : '--off'));
                        }

                        usleep(50000); //50ms nach dem Schaltbefehl warten

                        //Debug ausgabe
                        if ($this->debug) {

                            if(in_array($request['protocol'], $this->protocolsWithId)) {

                                $this->response->writeLnColored('sudo ' . $sendPath . ' -p ' . escapeshellarg($request['protocol']) . ' --id=' . escapeshellarg($systemCode) . ' --unit=' . escapeshellarg($request['deviceCode']) . ' ' . ($request['command'] == 1 ? '--on' : '--off') , 'light_blue');
                            } else {

                                $this->response->writeLnColored('sudo ' . $sendPath . ' -p ' . escapeshellarg($request['protocol']) . ' --systemcode=' . escapeshellarg($systemCode) . ' --unitcode=' . escapeshellarg($request['deviceCode']) . ' ' . ($request['command'] == 1 ? '--on' : '--off') , 'light_blue');
                            }
                        }
                    }

                    //Decrement
                    $requestData[$i]['continuous']--;
                    $requestData[$i]['time'] = microtime(true);
                } else {

                    unset($requests[$i]);
                }

            }

        }
    }

    /**
     * schreibt einen GPIO
     * 
     * @param Array   $request Anfrage
     */
    protected function writeGpio(array $request) {

        $gpioPath = $this->command->getSetting('shc.switchServer.gpioCommand');
        
        //Modus setzen
        if (!in_array($request['pinNumber'], $this->gpioModeSet)) {

            @shell_exec($gpioPath . ' mode ' . escapeshellarg($request['pinNumber']) . ' out');
            if ($this->debug) {

                $this->response->writeLnColored($gpioPath . ' mode ' . escapeshellarg($request['pinNumber']) . ' out', 'light_green');
            }
            $this->gpioModeSet[] = $request['pinNumber'];
        }

        //Befehl ausfuehren
        @shell_exec($gpioPath . ' write ' . escapeshellarg($request['pinNumber']) . ' ' . escapeshellarg($request['command']));
        if ($this->debug) {

            $this->response->writeLnColored($gpioPath . ' write ' . escapeshellarg($request['pinNumber']) . ' ' . escapeshellarg($request['command']), 'light_green');
        }
    }

    /**
     * liest eine GPIO und sendet die Antwort an den Client
     * 
     * @param \RWF\IO\SocketServerClient $client  Client Objekt
     * @param Array                      $request Anfrage
     */
    protected function readGpio(SocketServerClient $client, array $request) {

        $gpioPath = $this->command->getSetting('shc.switchServer.gpioCommand');
        
        //Modus setzen
        if (!in_array($request['pinNumber'], $this->gpioModeSet)) {

            @shell_exec($gpioPath . ' mode ' . escapeshellarg($request['pinNumber']) . ' in');
            $this->gpioModeSet[] = $request['pinNumber'];
            if ($this->debug) {

                $this->response->writeLnColored($gpioPath . ' mode ' . escapeshellarg($request['pinNumber']) . ' in', 'light_green');
            }
        }

        //Befehl ausfuehren
        @exec($gpioPath . ' read ' . escapeshellarg($request['pinNumber']), $data);

        //Daten Auswerten
        if(isset($data[0])) {

            $data = trim($data[0]);
            $response = array();
            if ((int)$data == 1) {

                $response['state'] = 1;
            } else {

                $response['state'] = 0;
            }
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

            $this->response->writeLnColored($gpioPath . ' read ' . escapeshellarg($request['pinNumber']), 'light_green');
            $this->response->writeLnColored((isset($data[0]) ? $data[0] : 'null'), 'light_green');
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
