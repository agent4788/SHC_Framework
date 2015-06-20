<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Luftfeuchte Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Humidity {

    /**
     * gibt die Luftfeuchte zurueck
     *
     * @return Integer
     */
    public function getHumidity();

    /**
     * gibt die Aktuelle Luftfeuchte vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayHumidity();

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $humidityOffset
     * @return \SHC\Sensor\Model\Humidity
     */
    public function setHumidityOffset($humidityOffset);

    /**
     * gbit das Temperatur Offset zurueck
     *
     * @return Float
     */
    public function getHumidityOffset();

    /**
     * setzt die Sichtbarkeit der Luftfeuchte
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Humidity
     */
    public function humidityVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Luftfeuchte an
     *
     * @return Boolean
     */
    public function isHumidityVisible();
}