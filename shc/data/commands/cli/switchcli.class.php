<?php

namespace SHC\Command\CLI;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\CliCommand;
use RWF\Util\JSON;
use SHC\Command\CommandSheduler;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * CLI Schnittstelle zum schalten von Schaltbaren elementen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchCli extends CliCommand {

    /**
     * kurzer Kommandozeilen Parameter
     *
     * @var String
     */
    protected $shortParam = '-sw';

    /**
     * voller Kommandozeilen Parameter
     *
     * @var String
     */
    protected $fullParam = '--switch';

    /**
     * Debug Modus aktiv
     *
     * @var Boolean
     */
    protected $debug = false;

    /**
     * gibt die Hilfe zu der Kommandozeilen Funktion auf die Kommandozeile aus
     */
    public function writeHelp() {

        //Sprache einbinden
        $r = RWF::getResponse();
        $r->writeLnColored('-sw oder --switch', 'green_u');
        $r->writeLn('');
        $r->writeLn('Mit diesem Befehl kann man schaltbare Elemente über die Kommandozeile schalten');
        $r->writeLn('');
        $r->writeLnColored('Erforderliche Parameter:', 'yellow_u');
        $r->writeLnColored("\t" . 'id=<Element ID>', 'yellow');
        $r->writeLn("\t\t" . 'die ID des zu schaltenden Elementes');
        $r->writeLnColored("\t" . '--on oder --off oder --toggle', 'yellow');
        $r->writeLn("\t\t" . 'der Befehl der ausgeführt werden soll');
        $r->writeLn("\t\t" . '--on Einschalten');
        $r->writeLn("\t\t" . '--off Ausschalten');
        $r->writeLn("\t\t" . '--toggle Umschalten');

        $r->writeLnColored('Zusätzliche Optionen:', 'yellow_u');
        $r->writeLnColored("\t" . '-l oder --list', 'yellow');
        $r->writeLn("\t\t" . 'gibt eine Liste mit allen verfuegbaren Elemeten un deren ID aus');
    }

    /**
     * konfiguriert das CLI Kommando
     */
    protected function config() {}

    protected function executeCliCommand() {

        //Initialisieren
        global $argv;
        RWF::getLanguage()->loadModul('room');
        $r = RWF::getResponse();

        //Liste mit allen Schaltbaren Elementen ausgeben
        if (in_array('-l', $argv) || in_array('--list', $argv)) {

            $switchables = SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME);
            //Kopfzeile
            $r->writeLn('***********************************************************************************************');
            $r->writeLn('*  ID * Name **********************************************************************************');
            foreach($switchables as $switchable) {

                /* @var $switchable \SHC\Switchable\Switchable */
                $r->writeLn('* '. str_pad($switchable->getId(), 3, ' ', STR_PAD_LEFT) .' * '. str_pad($switchable->getName(), 85) .' *');
            }

            $r->writeLn('***********************************************************************************************');
            return;
        }

        //JSON Modus zum schalten mehrere Elemente
        if (in_array('-j', $argv) || in_array('--json', $argv)) {

            foreach($argv as $param) {

                if (preg_match('#\[.*#', $param)) {

                    $json = JSON::decode($param);
                    foreach ($json as $id => $command) {

                        //schaltbares Element
                        $switchable = SwitchableEditor::getInstance()->getElementById($id);
                        if ($switchable instanceof Switchable) {

                            //Befehl
                            switch ($command) {

                                case 1:

                                    //an
                                    $switchable->switchOn();
                                    break;
                                case 2:

                                    //aus
                                    if ($switchable instanceof WakeOnLan || $switchable instanceof Reboot || $switchable instanceof Shutdown) {

                                        //diese Elemente koennen nicht ausgeschalten werden
                                        $r->writeLnColored('error 1', 'red');
                                        return;
                                    }
                                    $switchable->switchOff();
                                    break;
                                case 3:

                                    //umschalten
                                    if ($switchable instanceof WakeOnLan || $switchable instanceof Reboot || $switchable instanceof Shutdown) {

                                        //diese Elemente koennen nicht ausgeschalten werden
                                        $r->writeLnColored('error 2', 'red');
                                        return;
                                    }
                                    $switchable->toggle();
                                    break;
                                default:

                                    //ungueltiger Befehl
                                    $r->writeLnColored('error 3', 'red');
                                    return;
                            }
                        }
                    }

                    //Befehle senden
                    try {

                        CommandSheduler::getInstance()->sendCommands();
                        $r->writeLnColored('success', 'green');
                        SwitchableEditor::getInstance()->updateState();
                        return;
                    } catch (\Exception $e) {

                        if ($e->getCode() == 1510) {

                            //GPIO Schaltserver nicht errreicht
                            $r->writeLnColored(RWF::getLanguage()->get('error 4'), 'red');
                            return;
                        } elseif ($e->getCode() == 1511) {

                            //Schaltserver unterstuetzt kein GPIO schalten
                            $r->writeLnColored(RWF::getLanguage()->get('error 5'), 'red');
                            return;
                        } elseif ($e->getCode() == 1512) {

                            //kein Schaltserver erreichbar
                            RWF::getLanguage()->enableAutoHtmlEndocde();
                            $r->writeLnColored(RWF::getLanguage()->get('error 6'), 'red');
                            return;
                        }
                    }
                }
                break;
            }
            return;
        }

        //Element schalten
        $id = 0;
        $command = 0;

        //ID suchen
        foreach($argv as $param) {

            if(preg_match('#id\=(\d+)#', $param, $match)) {

                $id = intval($match[1]);
                break;
            }
        }

        //Befehl
        if (in_array('--on', $argv)) {

            //on
            $command = 1;
        } elseif (in_array('--off', $argv)) {

            //aus
            $command = 2;
        } elseif (in_array('--toggle', $argv)) {

            //umschalten
            $command = 3;
        }

        //schaltbares Element
        $switchable = SwitchableEditor::getInstance()->getElementById($id);
        if($switchable instanceof Switchable) {

            //Befehl
            switch($command) {

                case 1:

                    //an
                    $switchable->switchOn();
                    break;
                case 2:

                    //aus
                    if($switchable instanceof WakeOnLan || $switchable instanceof Reboot || $switchable instanceof Shutdown) {

                        //diese Elemente koennen nicht ausgeschalten werden
                        $r->writeLnColored('WakeOnLan, Neustart und Herunterfahren können nicht ausgeschalten werden', 'red');
                        return;
                    }
                    $switchable->switchOff();
                    break;
                case 3:

                    //umschalten
                    if($switchable instanceof WakeOnLan || $switchable instanceof Reboot || $switchable instanceof Shutdown) {

                        //diese Elemente koennen nicht ausgeschalten werden
                        $r->writeLnColored('WakeOnLan, Neustart und Herunterfahren können nicht ausgeschalten werden', 'red');
                        return;
                    }
                    $switchable->toggle();
                    break;
                default:

                    //ungueltiger Befehl
                    $r->writeLnColored('ungültiger Befehl', 'red');
                    return;
            }

            //Befehle senden
            try {

                CommandSheduler::getInstance()->sendCommands();
                $r->writeLnColored('Element erfolgreich geschalten', 'green');
                SwitchableEditor::getInstance()->updateState();
                return;
            } catch(\Exception $e) {

                if ($e->getCode() == 1510) {

                    //GPIO Schaltserver nicht errreicht
                    $r->writeLnColored(RWF::getLanguage()->get('index.room.error.1510'), 'red');
                    return;
                } elseif ($e->getCode() == 1511) {

                    //Schaltserver unterstuetzt kein GPIO schalten
                    $r->writeLnColored(RWF::getLanguage()->get('index.room.error.1511'), 'red');
                    return;
                } elseif ($e->getCode() == 1512) {

                    //kein Schaltserver erreichbar
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    $r->writeLnColored(RWF::getLanguage()->get('index.room.error.1512'), 'red');
                    return;
                }
            }
        }

        //ungueltige ID
        $r->writeLnColored('ungültige ID', 'red');
        return;
    }
}