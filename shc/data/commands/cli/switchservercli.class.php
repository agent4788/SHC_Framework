<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\CliCommand;
use SHC\SwitchServer\SwitchServerSocket;
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

        $r = RWF::getResponse();
        $r->writeLnColored('-ss oder --switchserver  startet den Schalt Server', 'green_u');
        $r->writeLn('');
        $r->writeLn('Der Schaltserver ist der 2. wichtige Dienst, er nimmt die Schaltaufgaben entgegen und sendet diese über den angeschlossenen 433MHz Sender an die Steckdosen.');
        $r->writeLn('Zusätzlich schaltet der Schaltserver auch die einzelnen GPIOs des Raspberry Pi.');
        $r->writeLn('');
        $r->writeLn('Damit das SHC richtig Funktioniert muss mindestens ein Schaltserver erreichbar sein.');
        $r->writeLn('In den Standardeinstellungen ist dieser Dienst aktiviert.');
        $r->writeLn('');

        $r->writeLnColored('Zusätzliche Optionen:', 'yellow_u');
        $r->writeLnColored("\t" . '-c oder --config', 'yellow');
        $r->writeLn("\t\t" . 'Hier kann die IP-Adresse und der Port des Servers festgelegt werden.');
        $r->writeLnColored("\t" . '-s oder --stop', 'yellow');
        $r->writeLn("\t\t" . 'Mit dieser Option kann ein Laufender Schaltserver gestoppt werden.');
        $r->writeLnColored("\t" . '-d oder --debug', 'yellow');
        $r->writeLn("\t\t" . 'Startet den Debug Modus, alle eingehenden Befehle werden auf der Kommandozeile ausgegeben.');
        $r->writeLn('');
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

        $switchServer = new SwitchServerSocket();
        $switchServer->run($this->response, $this->debug);
    }

}
