<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Temperatur Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Temperature {

    /**
     * gibt den Aktuellen Temperaturwert zurueck
     *
     * @return Float
     */
    public function getTemperature();

    /**
     * gibt den Aktuellen Temperaturwert vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayTemperature();

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Model\Temperature
     */
    public function setTemperatureOffset($temperatureOffset);

    /**
     * gbit das Temperatur Offset zurueck
     *
     * @return Float
     */
    public function getTemperatureOffset();

    /**
     * setzt die Sichtbarkeit der Temperatur
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Temperature
     */
    public function temperatureVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Temperatur an
     *
     * @return Boolean
     */
    public function isTemperatureVisible();
}