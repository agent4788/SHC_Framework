<?php

/**
 * Eintrittspunkt in die Anwendungen
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */

//Error Handling
error_reporting(E_ALL | E_ERROR | E_NOTICE | E_PARSE | E_STRICT | E_WARNING);

//Includes
require_once(__DIR__ . '/rwf/global.php');

$class = '\\' . APP_NAME .'\\core\\' . APP_NAME;
/* @var $app RWF\Core\RWF */
$app = new $class();

var_dump($app);

$app->finalize();