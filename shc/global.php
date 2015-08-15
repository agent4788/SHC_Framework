<?php

/**
 * Grundeinstellungen des SHC
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
//SHC Grundkonfiguration
define('PATH_SHC', __DIR__ . '/');                                               //Pfad zum Hauptordner des SHC
define('PATH_SHC_CLASSES', __DIR__ . '/lib/');                                   //Pfad zum Klassenordner des SHC
define('PATH_SHC_STORAGE', PATH_SHC . 'data/storage/');                          //Pfad zum Speicherordner des SHC
define('PATH_SHC_LOG', PATH_SHC . 'data/log/');                                  //Pfad zum Logordner des SHC
define('COMMAND_DIRS', PATH_SHC . 'data/commands/');                             //Speicherorte der Kommando Klassen mehere Pfade durch Semikolon (;) getrennt

//Namensraum SHC registrieren
RWF\ClassLoader\ClassLoader::getInstance()->registerBaseNamespace('SHC', PATH_SHC_CLASSES);
