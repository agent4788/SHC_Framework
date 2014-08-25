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
define('PATH_BASE', dirname(__DIR__) . '/');                                     //Pfad zum Hauptordner der Anwendung
//RWF Grundkonfiguration
define('PATH_RWF', __DIR__ . '/');                                               //Pfad zum Hauptordner des Frameworks
define('PATH_RWF_CLASSES', __DIR__ . '/lib/');                                   //Pfad zum Klassenordner des Frameworks
define('PATH_RWF_STORAGE', PATH_RWF . 'data/storage/');                         //Pfad zum Speicherordner des Frameworks
define('PATH_RWF_CACHE', PATH_RWF . 'data/cache/');                             //Pfad zum Cacheordner des Frameworks
define('PATH_RWF_LOG', PATH_RWF . 'data/log/');                                 //Pfad zum Logordner des Frameworks
define('PATH_RWF_SESSION', PATH_RWF . 'data/cache/session/');                   //Pfad zum Sessionordner des Frameworks
//Grundeinstellungen
define('DEVELOPMENT_MODE', true);                                               //Aktiviert/Deaktiviert den Entwicklermodus
//Konstanten fur Laufzeitueberwachung
define('TIME_NOW', time());
define('MICROTIME_NOW', strtok(microtime(), ' ') + strtok(''));

//Fehlerbehandlung initalisieren
require_once(PATH_RWF_CLASSES . 'error/error.class.php');
$error = new RWF\Error\Error();
$error->enableDisplayErrors(true); //später mit DEVELOPMENT_MODE verknuepfen
$error->enableLogErrors(true);

//APP Namen ermitteln und Pruefen
if (!isset($_GET['app']) || !file_exists(PATH_BASE . $_GET['app']) || !preg_match('#^[a-z0-9]#i', $_GET['app'])) {

    //Fehler (App Name muss zwingend uebergeben werden
    throw new Exception('Die App ist nicht bekannt', 1010);
}
define('APP_NAME', strtolower($_GET['app']));

//Autoload Klassen Laden
require_once(PATH_RWF_CLASSES . 'classloader/classloader.class.php');
require_once(PATH_RWF_CLASSES . 'classloader/exception/classnotfoundexception.class.php');

//Einbinden der Globalen Kofiguration der Anwendung
require_once(PATH_BASE . APP_NAME . '/global.php');

//Autoload durchfuehren
function __autoload($class) {

    if (DEVELOPMENT_MODE) {

        //Autoload im Entwicklermodus
        \RWF\ClassLoader\ClassLoader::getInstance()->loadClass($class);
    } else {

        //Packen und Laden der gesamten Klassendatei
        \RWF\ClassLoader\ClassLoader::getInstance()->loadAllClasses($class);
    }
}