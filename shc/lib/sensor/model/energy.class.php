<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Energieverbrauch Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface Energy {

    /**
     * gibt den Aktuellen Leistungsverbrauch zurueck
     *
     * @return Float
     */
    public function getEnergy();

    /**
     * gibt den Aktuellen Leistungsverbrauch vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayEnergy();

    /**
     * setzt die Sichtbarkeit des Leistungsverbrauchs
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Energy
     */
    public function energyVisibility($visibility);

    /**
     * gibt die Sichtbarkeit des Leistungsverbrauchs an
     *
     * @return Boolean
     */
    public function isEnergyVisible();
}