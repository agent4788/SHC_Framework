<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Feuchtigkeits Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractMoisture {

    /**
     * Feuchtigkeit
     *
     * @var Integer
     */
    protected $moisture = 0;

    /**
     * Feuchtigkeit Anzeigen
     *
     * @var Integer
     */
    protected $moistureVisibility = 1;

    /**
     * Feuchtigkeits Offset
     *
     * @var Integer
     */
    protected $moistureOffset = 0;

    /**
     * gibt die Aktuelle Feuchtigkeit zurueck
     *
     * @return Float
     */
    public function getMoisture() {

        return $this->moisture + $this->moistureOffset;
    }

    /**
     * gibt die Aktuelle Feuchtigkeit vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayMoisture() {

        return String::formatInteger($this->getMoisture() * 100 / 1023) .'%';
    }

    /**
     * setzt das Feuchtigkeits Offset
     *
     * @param  Float $moistureOffset
     * @return \SHC\Sensor\Model\Moisture
     */
    public function setMoistureOffset($moistureOffset) {

        $this->moistureOffset = $moistureOffset;
        return $this;
    }

    /**
     * gbit das Feuchtigkeits Offset zurueck
     *
     * @return Float
     */
    public function getMoistureOffset() {

        return $this->moistureOffset;
    }

    /**
     * setzt die Sichtbarkeit der Feuchtigkeit
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Moisture
     */
    public function moistureVisibility($visibility) {

        $this->moistureVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Feuchtigkeit an
     *
     * @return Boolean
     */
    public function isMoistureVisible() {

        return ($this->moistureVisibility == 1 ? true : false);
    }
}