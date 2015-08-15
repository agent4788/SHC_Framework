<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Stromverbrauch Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Power {

    /**
     * gibt den Aktuellen Stromverbrauch zurueck
     *
     * @return Float
     */
    public function getPower();

    /**
     * gibt den Aktuellen Stromverbrauch vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayPower();

    /**
     * setzt die Sichtbarkeit des Stromverbrauchs
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Power
     */
    public function powerVisibility($visibility);

    /**
     * gibt die Sichtbarkeit des Stromverbrauchs an
     *
     * @return Boolean
     */
    public function isPowerVisible();
}