<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Entfernung Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Distance {

    /**
     * gibt die Entfernung zurueck
     *
     * @return Float
     */
    public function getDistance();

    /**
     * gibt die Entfernung vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayDistance();

    /**
     * setzt das Entfernungs Offset
     *
     * @param  Float $distanceOffset
     * @return \SHC\Sensor\Model\Distance
     */
    public function setDistanceOffset($distanceOffset);

    /**
     * gbit das Entfernungs Offset zurueck
     *
     * @return Float
     */
    public function getDistanceOffset();

    /**
     * setzt die Sichtbarkeit der Entfernung
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Distance
     */
    public function distanceVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Entfernung an
     *
     * @return Boolean
     */
    public function isDistanceVisible();
}