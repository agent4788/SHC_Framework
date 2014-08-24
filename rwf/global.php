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
define('PATH_BASE', dirname(__DIR__) .'/');                                      //Pfad zum Hauptordner der Anwendung

//RWF Grundkonfiguration
define('PATH_RWF', __DIR__ .'/');                                                //Pfad zum Hauptordner des Frameworks
define('PATH_RWF_CLASSES', __DIR__ .'/lib/');                                    //Pfad zum Klassenordner des Frameworks
define('PATH_RWF_STORAGE', PATH_RWF . 'data/storage/');                         //Pfad zum Speicherordner des Frameworks
define('PATH_RWF_CACHE', PATH_RWF . 'data/cache/');                             //Pfad zum Cacheordner des Frameworks
define('PATH_RWF_LOG', PATH_RWF . 'data/log/');                                 //Pfad zum Logordner des Frameworks
define('PATH_RWF_SESSION', PATH_RWF . 'data/cache/session/');                   //Pfad zum Sessionordner des Frameworks

//Grundeinstellungen
define('DEVELOPMENT_MOD', true);                                                //Aktiviert/Deaktiviert den Entwicklermodus

//Konstanten fur Laufzeitueberwachung
define('TIME_NOW', time());
define('MICROTIME_NOW', strtok(microtime(), ' ') + strtok(''));
