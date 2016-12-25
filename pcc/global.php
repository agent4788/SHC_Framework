<?php

/**
 * Grundeinstellungen des PCC
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.4-0
 * @version    2.0.4-0
 */
//SHC Grundkonfiguration
define('PATH_PCC', __DIR__ . '/');                                               //Pfad zum Hauptordner des PCC
define('PATH_PCC_CLASSES', __DIR__ . '/lib/');                                   //Pfad zum Klassenordner des PCC
define('PATH_PCC_STORAGE', PATH_PCC . 'data/storage/');                          //Pfad zum Speicherordner des PCC
define('PATH_PCC_LOG', PATH_PCC . 'data/log/');                                  //Pfad zum Logordner des PCC
define('COMMAND_DIRS', PATH_PCC . 'data/commands/');                             //Speicherorte der Kommando Klassen mehere Pfade durch Semikolon (;) getrennt

//Namensraum PCC registrieren
RWF\ClassLoader\ClassLoader::getInstance()->registerBaseNamespace('PCC', PATH_PCC_CLASSES);