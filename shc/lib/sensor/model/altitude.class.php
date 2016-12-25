<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Hoehe Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Altitude {

    /**
     * gibt den Aktuellen Temperaturwert zurueck
     *
     * @return Float
     */
    public function getAltitude();

    /**
     * gibt den Aktuellen Temperaturwert vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayAltitude();

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Model\Altitude
     */
    public function setAltitudeOffset($altitudeOffset);

    /**
     * gbit das Temperatur Offset zurueck
     *
     * @return Float
     */
    public function getAltitudeOffset();

    /**
     * setzt die Sichtbarkeit der Temperatur
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Altitude
     */
    public function altitudeVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Temperatur an
     *
     * @return Boolean
     */
    public function isAltitudeVisible();
}