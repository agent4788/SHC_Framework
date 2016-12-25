<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Feuchtigkeits Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Moisture {

    /**
     * gibt die Aktuelle Feuchtigkeit zurueck
     *
     * @return Float
     */
    public function getMoisture();

    /**
     * gibt die Aktuelle Feuchtigkeit vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayMoisture();

    /**
     * setzt das Feuchtigkeits Offset
     *
     * @param  Float $moistureOffset
     * @return \SHC\Sensor\Model\Moisture
     */
    public function setMoistureOffset($moistureOffset);

    /**
     * gbit das Feuchtigkeits Offset zurueck
     *
     * @return Float
     */
    public function getMoistureOffset();

    /**
     * setzt die Sichtbarkeit der Feuchtigkeit
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Moisture
     */
    public function moistureVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Feuchtigkeit an
     *
     * @return Boolean
     */
    public function isMoistureVisible();
}