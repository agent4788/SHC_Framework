<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Energieverbrauch Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractEnergy {

    /**
     * entnomme Leistung in Wh
     *
     * @var int
     */
    protected $energy = 0;

    /**
     * Leistung Anzeigen
     *
     * @var Integer
     */
    protected $energyVisibility = 1;

    /**
     * gibt den Aktuellen Leistungsverbrauch zurueck
     *
     * @return Float
     */
    public function getEnergy() {

        return $this->energy;
    }

    /**
     * gibt den Aktuellen Leistungsverbrauch vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayEnergy() {

        return ($this->getEnergy() < 1000.0 ? String::formatFloat($this->getEnergy(), 0) .' Wh' : String::formatFloat($this->getEnergy() / 1000, 2) .' kWh');
    }

    /**
     * setzt die Sichtbarkeit des Leistungsverbrauchs
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Energy
     */
    public function energyVisibility($visibility) {

        $this->energyVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit des Leistungsverbrauchs an
     *
     * @return Boolean
     */
    public function isEnergyVisible() {

        return ($this->energyVisibility == 1 ? true : false);
    }
}