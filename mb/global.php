<?php

/**
 * Grundeinstellungen der MovieBase
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.4-0
 * @version    2.0.4-0
 */
//SHC Grundkonfiguration
define('PATH_MB', __DIR__ . '/');                                              //Pfad zum Hauptordner des MB
define('PATH_MB_CLASSES', __DIR__ . '/lib/');                                  //Pfad zum Klassenordner des MB
define('PATH_MB_STORAGE', PATH_MB . 'data/storage/');                          //Pfad zum Speicherordner des MB
define('PATH_MB_LOG', PATH_MB . 'data/log/');                                  //Pfad zum Logordner des MB
define('COMMAND_DIRS', PATH_MB . 'data/commands/');                            //Speicherorte der Kommando Klassen mehere Pfade durch Semikolon (;) getrennt

//Namensraum PCC registrieren
RWF\ClassLoader\ClassLoader::getInstance()->registerBaseNamespace('MB', PATH_MB_CLASSES);