<?php

namespace SHC\Sensor;

//Imports

/**
 * Schnittstelle vSensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface vSensor {

    /**
     * gibt eine Liste mit den IDs der Sensoren zurück
     *
     * @return mixed
     */
    public function listSensorIDs();

    /**
     * verarbeitet die Daten der einzelnen Sensoren
     */
    public function processData();
}