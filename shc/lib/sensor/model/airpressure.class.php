<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Luftdruck Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface AirPressure {

    /**
     * gibt den Aktuellen Luftdruck zurueck
     *
     * @return Float
     */
    public function getAirPressure();

    /**
     * gibt den Aktuellen Luftdruck vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayAirPressure();

    /**
     * setzt das Luftdruck Offset
     *
     * @param  Float $airPressureOffset
     * @return \SHC\Sensor\Model\AirPressure
     */
    public function setAirPressureOffset($airPressureOffset);

    /**
     * gbit das Luftdruck Offset zurueck
     *
     * @return Float
     */
    public function getAirPressureOffset();

    /**
     * setzt die Sichtbarkeit der Luftdruck
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\AirPressure
     */
    public function airPressureVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Luftdruck an
     *
     * @return Boolean
     */
    public function isAirPressureVisible();
}