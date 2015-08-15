<?php

/**
 * Grundeinstellungen des Frameworks
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
//Globale Konstanten
define('PATH_BASE', dirname(__DIR__) . '/');                                    //Pfad zum Hauptordner der Anwendung
//RWF Grundkonfiguration
define('PATH_RWF', __DIR__ . '/');                                              //Pfad zum Hauptordner des Frameworks
define('PATH_RWF_CLASSES', __DIR__ . '/lib/');                                  //Pfad zum Klassenordner des Frameworks
define('PATH_RWF_STORAGE', PATH_RWF . 'data/storage/');                         //Pfad zum Speicherordner des Frameworks
define('PATH_RWF_CACHE', PATH_RWF . 'data/cache/');                             //Pfad zum Cacheordner des Frameworks
define('PATH_RWF_CACHE_TEMPLATES', PATH_RWF_CACHE . 'templates/');              //Pfad zum Cacheordner der Templates des Frameworks
define('PATH_RWF_LOG', PATH_RWF . 'data/log/');                                 //Pfad zum Logordner des Frameworks
define('PATH_RWF_SESSION', PATH_RWF . 'data/cache/session/');                   //Pfad zum Sessionordner des Frameworks
define('PATH_RWF_BACKUP', PATH_RWF . 'data/backup/');                          //Pfad zum Backupordner des Frameworks
//Grundeinstellungen
define('DEVELOPMENT_MODE', true);                                               //Aktiviert/Deaktiviert den Entwicklermodus
define('RWF_COOKIE_PREFIX', 'rwf_');                                            //Cookie Prefix
define('RWF_GUEST_USER_GROUP', 3);                                              //Benutzergruppe fuer Gaeste
//Konstanten fur Laufzeitueberwachung
define('TIME_NOW', time());
define('MICROTIME_NOW', strtok(microtime(), ' ') . strtok(''));
//Zugriffsmethode
if (PHP_SAPI == 'cli') {

    //per Kommandozeile
    define('ACCESS_METHOD_CLI', true);
    define('ACCESS_METHOD_HTTP', false);
} else {

    //Per Browser
    define('ACCESS_METHOD_CLI', false);
    define('ACCESS_METHOD_HTTP', true);
}

//Fehlerbehandlung initalisieren
require_once(PATH_RWF_CLASSES . 'error/error.class.php');
$error = new RWF\Error\Error();
$error->enableDisplayErrors(true); //später mit DEVELOPMENT_MODE verknuepfen
$error->enableLogErrors(true);

//APP Namen ermitteln und Pruefen
if (ACCESS_METHOD_HTTP && (!isset($_GET['app']) || !file_exists(PATH_BASE . $_GET['app']) || !preg_match('#^[a-z0-9]#i', $_GET['app']))) {

    //Fehler (App Name muss zwingend uebergeben werden
    throw new Exception('Die App ist nicht bekannt', 1010);
} elseif (ACCESS_METHOD_CLI && (!isset($argv[1]) || !preg_match('#app=(.+)#', $argv[1]))) {

    //Fehler (App Name muss zwingend uebergeben werden
    throw new Exception('Die App ist nicht bekannt', 1010);
} elseif (ACCESS_METHOD_CLI) {

    $matches = array();
    if (preg_match('#app=(.+)#', $argv[1], $matches)) {

        define('APP_NAME', strtolower($matches[1]));
    } else {

        //Fehler (App Name muss zwingend uebergeben werden
        throw new Exception('Die App ist nicht bekannt', 1010);
    }
} else {

    define('APP_NAME', strtolower($_GET['app']));
}

//Autoload Klassen Laden
require_once(PATH_RWF_CLASSES . 'classloader/classloader.class.php');
require_once(PATH_RWF_CLASSES . 'classloader/exception/classnotfoundexception.class.php');

//Einbinden der Globalen Kofiguration der Anwendung
require_once(PATH_BASE . APP_NAME . '/global.php');

//Autoload durchfuehren
//if (DEVELOPMENT_MODE === true) {
//
    function __autoload($class) {

        \RWF\ClassLoader\ClassLoader::getInstance()->loadClass($class);
    }
//
//} else {
//
//    RWF\ClassLoader\ClassLoader::getInstance()->icnludeClasses();
//}

