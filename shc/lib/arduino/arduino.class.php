<?php

namespace SHC\Arduino;

//Imports


/**
 * Arduino verwaltungsklasse
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class Arduino {

    /**
     * Arduino Pro Mini (13 I/O)
     *
     * @var int
     */
    const PRO_MINI = 65536;

    /**
     * Arduino Nano (13 I/O)
     *
     * @var int
     */
    const NANO = 131072;

    /**
     * Arduino Uno (13 I/O)
     *
     * @var int
     */
    const UNO = 262144;

    /**
     * Arduino Mega (54 I/O)
     *
     * @var int
     */
    const MEGA = 524288;

    /**
     * Arduino DUE (54 I/O)
     *
     * @var int
     */
    const DUE = 1048576;

    /**
     * ESP8266-01 WLAN Chip (2 GPIO)
     *
     * @var int
     */
    const ESP8266_01 = 2097152;

    /**
     * ESP8266-12 WLAN Chip (16 GPIO)
     *
     * @var int
     */
    const ESP8266_12 = 4194308;
}