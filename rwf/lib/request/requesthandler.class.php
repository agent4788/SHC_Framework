<?php

namespace RWF\Request;

//Imports
use RWF\Core\RWF;
use RWF\Util\DataTypeUtil;
use RWF\Util\FileUtil;
use RWF\Request\Commands\CliCommand;

/**
 * Kernklasse (initialisiert das RWF)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RequestHandler {

    /**
     * Seite
     * 
     * @var String
     */
    const PAGE = 'page';

    /**
     * Aktion
     * 
     * @var String
     */
    const ACTION = 'action';

    /**
     * AJAX Anfrage
     * 
     * @var String
     */
    const AJAX = 'ajax';

    /**
     * Syncronisations Anfrage
     * 
     * @var String
     */
    const SYNC = 'sync';

    /**
     * Kommandozeilen Anfrage
     * 
     * @var String
     */
    const CLI = 'cli';

    /**
     * Anfrageobjekt
     * 
     * @var RWF\Request\Request
     */
    protected static $request = null;

    /**
     * Anfrageobjekt
     * 
     * @var RWF\Request\Response
     */
    protected static $response = null;

    /**
     * Liste mit allen CLI Kommandos
     * 
     * @var Array
     */
    protected $cliCommands = array();

    /**
     * behandelt eine Anfrage an die Anwendung
     */
    public static function handleRequest() {

        //Anfrage/Antwortobjekt holen
        self::$request = $r = RWF::getRequest();
        self::$response = RWF::getResponse();

        //Angeforderten Geraetetyp ermitteln
        if ($r->issetParam('m')) {

            //Smartphone Ansicht
            define('RWF_DEVICE', 'smartphone');
        } elseif ($r->issetParam('t')) {

            //Tablet Ansicht
            define('RWF_DEVICE', 'tablet');
        } elseif ($r->issetParam('g')) {

            //fuer alle Geraetetypen
            define('RWF_DEVICE', 'global');
        } else {

            //PC/Web Ansicht
            define('RWF_DEVICE', 'web');
        }

        //Anfragetyp ermitteln
        if ($r->issetParam(self::PAGE) && ACCESS_METHOD_HTTP) {

            //Seite
            new RequestHandler(self::PAGE, $r->getParam(self::PAGE, Request::GET, DataTypeUtil::STRING));
        } elseif ($r->issetParam(self::ACTION) && ACCESS_METHOD_HTTP) {

            //Aktion
            new RequestHandler(self::ACTION, $r->getParam(self::ACTION, Request::GET, DataTypeUtil::STRING));
        } elseif ($r->issetParam(self::AJAX) && ACCESS_METHOD_HTTP) {

            //AJAX Anfrage
            new RequestHandler(self::AJAX, $r->getParam(self::AJAX, Request::GET, DataTypeUtil::STRING));
        } elseif ($r->issetParam(self::SYNC) && ACCESS_METHOD_HTTP) {

            //Syncronisations Anfrage
            new RequestHandler(self::SYNC, $r->getParam(self::SYNC, Request::GET, DataTypeUtil::STRING));
        } elseif (ACCESS_METHOD_HTTP) {

            //Startseite Anzeigen
            new RequestHandler(self::PAGE, 'index');
        } elseif (ACCESS_METHOD_CLI) {

            //Kommandozeilen Anfrage behandeln
            new RequestHandler(self::CLI, '');
        } else {

            //Fehler Anfragetyp nicht bekann
            throw new \Exception('Unbekannter Anfragetyp', 1020);
        }
    }

    /**
     * @param  String $requestType     Anfragetyp
     * @param  String $requestedObject Anfrage Objekt
     * @throws \Exception
     */
    public function __construct($requestType, $requestedObject) {

        if ($requestType === self::CLI) {

            //Kommandozeilen Anfrage behandeln
            $this->handleCliRequest();
        } else {

            //Web Anfrage behandeln
            $this->handleWebRequest($requestType, $requestedObject);
        }
    }

    /**
     * Anfrage vom Webbroser behandeln
     * 
     * @param  String $requestType     Anfragetyp
     * @param  String $requestedObject Anfrage Objekt
     * @throws \Exception
     */
    protected function handleWebRequest($requestedObject) {

        //Objektname pruefen
        if (!preg_match('#^[a-z0-9]+$#i', $requestedObject)) {

            //Fehler nicht erlaubte Zeichen in der Anfrage
            throw new \Exception('Nicht erlaubter Name für die Anfrage', 1021);
        }

        //Speicherorte der Kommandoklassen
        $commandDirs = explode(';', COMMAND_DIRS);

        //Name der Controllerklasse ermitteln (virtueller Namensraum [also ohne direkte Zuordnung im Dateisystem])
        $className = '\\' . APP_NAME . '\\command\\' . RWF_DEVICE . '\\' . strtolower($requestedObject) . $requestType;
        $classFileName = strtolower($requestedObject) . $requestType . '.class.php';

        //Pfad der Datei suchen
        foreach ($commandDirs as $dir) {

            $searchPath = FileUtil::addTrailigSlash($dir) . RWF_DEVICE;
            $path = FileUtil::scannDirectory($searchPath, $classFileName);
        }

        if ($path !== null) {

            //Datei laden 
            require_once($path);

            //pruefen ob die Klasse nun bekannt ist
            if (!class_exists($className, false)) {

                //Fehler Klasse konnte nicht Galaden werden
                throw new \Exception('Die Kommandoklasse konnte nicht geladen werden', 1023);
            }

            //Templateorner registrieren
            RWF::getTemplate()->addTemplateDir(dirname($path));

            /* @var $command Command */
            $command = new $className();
            $command->execute(self::$request, self::$response);

            //Daten Senden
            self::$response->flush();
        } else {

            //Fehler Datei nicht gefunden
            throw new \Exception('Unbekannte Anfrage', 1022);
        }
    }

    /**
     * behandelt eine ANfrage auf der Kommandozeile
     * 
     * @throws \Exception
     */
    protected function handleCliRequest() {

        global $argv;

        //Kommandoobjekte Laden
        $this->loadCliCommands(PATH_BASE . APP_NAME . '/data/commands/cli');

        //aufgerufenenes Objekt sichen
        foreach ($this->cliCommands as $cliCommand) {

            /* @var $cliCommand \RWF\Request\Commands\CliCommand */
            if (in_array($cliCommand->getShortParam(), $argv) || in_array($cliCommand->getFullParam(), $argv)) {

                //Objekt gefunden
                $cliCommand->execute(self::$request, self::$response);
                //Daten Senden
                self::$response->flush();
                return;
            }
        }

        //Hilfe ohne Objektaufruf (Hilfe aller Objekte ausgeben)
        if (in_array('-h', $argv) || in_array('--help', $argv)) {

            foreach ($this->cliCommands as $cliCommand) {

                $cliCommand->execute(self::$request, self::$response);
                //Daten Senden
                self::$response->flush();
            }
            return;
        }

        //kein Objekt gefunden
        throw new \Exception('unbekanntes Kommando mit "-h" oder "--help" kannst du dir anzeigen lassen welche Kommandos es gibt', 1900);
    }

    /**
     * list alle verfuegbaren CLI Kommandos in eine Liste ein
     * 
     * @param String $path Pfad
     */
    protected function loadCliCommands($path) {

        //Objektdateien Laden, Objekte initialisieren
        $path = FileUtil::addTrailigSlash($path);
        $dir = opendir($path);

        //Dateien Einlesen
        while ($file = readdir($dir)) {

            //. und .. ignorieren
            if ($file == '.' || $file == '..') {

                continue;
            }

            //Unterordner Scannen
            if (is_dir($path . $file)) {

                $this->loadCliCommands($path . $file);
                continue;
            }

            //Datei
            if (is_file($path . $file) && preg_match('#.+cli\.class\.php$#i', $file)) {

                //Objektdatei includieren
                require_once($path . $file);

                //Klassenname
                $className = '\\' . APP_NAME . '\\Command\\CLI\\' . str_replace('.class.php', '', $file);
                $obj = new $className();

                //Pruefen ob das Objekt ein CLI Kommando ist
                if ($obj instanceof CliCommand) {

                    $this->cliCommands[] = $obj;
                } else {

                    $obj = null;
                }
            }
        }

        //Ordner Schliesen
        closedir($dir);
    }

}
