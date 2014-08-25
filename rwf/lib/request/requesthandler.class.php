<?php

namespace RWF\Request;

//Imports
use RWF\Core\RWF;
use RWF\Util\DataTypeUtil;
use RWF\Util\FileUtil;

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
        } elseif (false) {

            //Kommandozeilen Anfrage
            //Muss noch implementiert werden
        } elseif (ACCESS_METHOD_CLI) {

            //Kommandozeilen Anfrage bei aufruf ohne Parameter
            //Muss noch implementiert werden
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

}
