<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\CliCommand;
use RWF\Util\CliUtil;
use RWF\Util\String;
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

        $cli = new CliUtil();
        $response = $this->response;

        //Dienst aktiv
        $n = 0;
        $valid = true;
        $valid_active = false;
        $active_not_change = false;
        while ($n < 5) {

            $sender = $cli->input(RWF::getLanguage()->get('switchServer.input.active', (RWF::getSetting('shc.switchServer.active') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $active_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.active.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')$#i', $sender)) {

                $valid_active = true;
                break;
            } elseif ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $valid_active = false;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.active.invalid.repeated'), 'red');
            exit(1);
        }

        //IP Adresse
        $n = 0;
        $valid = true;
        $valid_address = '';
        $address_not_change = false;
        while ($n < 5) {

            $address = $cli->input(RWF::getLanguage()->get('switchServer.input.ip', RWF::getSetting('shc.switchServer.ip')));

            //Adresse nicht aendern
            if (String::length($address) == 0) {

                $address_not_change = true;
                $valid = true;
                break;
            }

            //Adresse pruefen
            $parts = explode('.', $address);
            for ($i = 0; $i < 3; $i++) {

                if (isset($parts[$i]) && (int) $parts[$i] >= 0 && (int) $parts[$i] <= 255) {

                    continue;
                }

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.ip.invalid'), 'red');
                $n++;
                $valid = false;
                break;
            }

            if ($valid === true) {

                $valid_address = $address;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.ip.invalid.repeated'), 'red');
            exit(1);
        }

        //Port
        $n = 0;
        $valid = true;
        $valid_port = '';
        $port_not_change = false;
        while ($n < 5) {

            $port = $cli->input(RWF::getLanguage()->get('switchServer.input.port', RWF::getSetting('shc.switchServer.port')));

            //Port nicht aendern
            if (String::length($port) == 0) {

                $port_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^[0-9]{1,5}$#', $port) || (int) $port <= 0 || (int) $port >= 65000) {

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.port.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true) {

                $valid_port = $port;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.port.invalid.repeated'), 'red');
            exit(1);
        }

        //Sende LED
        $n = 0;
        $valid = true;
        $valid_ledpin = '';
        $ledpin_not_change = false;
        while ($n < 5) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.ledPin.inactive'), 'yellow');
            $pin = $cli->input(RWF::getLanguage()->get('switchServer.input.ledPin', RWF::getSetting('shc.switchServer.sendLedPin')));

            //Port nicht aendern
            if (String::length($pin) == 0) {

                $ledpin_not_change = true;
                $valid = true;
                break;
            }

            if ((int) $pin < -1 || (int) $pin > 31) {

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.ledPin.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true) {

                $valid_ledpin = $pin;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.ledPin.invalid.repeated'), 'red');
            exit(1);
        }

        //Sender
        $n = 0;
        $valid = true;
        $valid_sender = false;
        $sender_not_change = false;
        while ($n < 5) {

            $sender = $cli->input(RWF::getLanguage()->get('switchServer.input.senderActive', (RWF::getSetting('shc.switchServer.senderActive') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $sender_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.senderActive.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')$#i', $sender)) {

                $valid_sender = true;
                break;
            } elseif ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $valid_sender = false;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.senderActive.invalid.repeated'), 'red');
            exit(1);
        }

        //GPIO Lesen
        $n = 0;
        $valid = true;
        $valid_gpio_read = false;
        $gpio_read_not_change = false;
        while ($n < 5) {

            $sender = $cli->input(RWF::getLanguage()->get('switchServer.input.gpioRead', (RWF::getSetting('shc.switchServer.readGpio') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $gpio_read_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.gpioRead.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')$#i', $sender)) {

                $valid_gpio_read = true;
                break;
            } elseif ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $valid_gpio_read = false;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.gpioRead.invalid.repeated'), 'red');
            exit(1);
        }

        //GPIO schreiben
        $n = 0;
        $valid = true;
        $valid_gpio_write = false;
        $gpio_write_not_change = false;
        while ($n < 5) {

            $sender = $cli->input(RWF::getLanguage()->get('switchServer.input.gpioWrite', (RWF::getSetting('shc.switchServer.writeGpio') == true ? RWF::getLanguage()->get('global.yes') : RWF::getLanguage()->get('global.no'))));

            //Port nicht aendern
            if (String::length($sender) == 0) {

                $gpio_write_not_change = true;
                $valid = true;
                break;
            }

            if (!preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')|('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.gpioWrite.invalid'), 'red');
                $n++;
                $valid = false;
                continue;
            }

            if ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.yes') .')|('. RWF::getLanguage()->get('global.yes.short') .')$#i', $sender)) {

                $valid_gpio_write = true;
                break;
            } elseif ($valid === true && preg_match('#^('. RWF::getLanguage()->get('global.no') .')|('. RWF::getLanguage()->get('global.no.short') .')$#i', $sender)) {

                $valid_gpio_write = false;
                break;
            }
        }

        if ($valid === false) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.gpioWrite.invalid.repeated'), 'red');
            exit(1);
        }

        //Speichern
        if($active_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.active', $valid_active);
        }
        if($address_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.ip', $valid_address);
        }
        if($port_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.port', $valid_port);
        }
        if($ledpin_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.sendLedPin', $valid_ledpin);
        }
        if($sender_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.senderActive', $valid_sender);
        }
        if($gpio_read_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.readGpio', $valid_gpio_read);
        }
        if($gpio_write_not_change === false) {

            RWF::getSettings()->editSetting('shc.switchServer.writeGpio', $valid_gpio_write);
        }

        try {

            RWF::getSettings()->saveAndReload();
            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.save.success'), 'green');
        } catch(\Exception $e) {

            $response->writeLnColored(RWF::getLanguage()->get('switchServer.input.save.error'), 'red');
        }
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

        //pruefen on Server aktiviert
        if (!RWF::getSetting('shc.switchServer.active')) {

            throw new \Exception('Der Schaltserver wurde deaktiviert', 1600);
        }

        $switchServer = new SwitchServerSocket();
        $switchServer->run($this->response, $this->debug);
    }

}
