<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;


/**
 * Luftfeuchte Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractHumidity  {

    /**
     * Luftfeuchtigkeit
     *
     * @var Integer
     */
    protected $humidity = 0;

    /**
     * Luftfeuchte Anzeigen
     *
     * @var Integer
     */
    protected $humidityVisibility = 1;

    /**
     * Luftfeuchte Offset
     *
     * @var Float
     */
    protected $humidityOffset = 0.0;

    /**
     * gibt die Luftfeuchte zurueck
     *
     * @return Integer
     */
    public function getHumidity() {

        return $this->humidity + $this->humidityOffset;
    }

    /**
     * gibt die Aktuelle Luftfeuchte vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayHumidity() {

        return String::formatFloat($this->getHumidity(), 1) .'%';
    }

    /**
     * setzt das Temperatur Offset
     *
     * @param  Float $humidityOffset
     * @return \SHC\Sensor\Model\Humidity
     */
    public function setHumidityOffset($humidityOffset) {

        $this->humidityOffset = $humidityOffset;
        return $this;
    }

    /**
     * gbit das Temperatur Offset zurueck
     *
     * @return Float
     */
    public function getHumidityOffset() {

        return $this->humidityOffset;
    }

    /**
     * setzt die Sichtbarkeit der Luftfeuchte
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\Humidity
     */
    public function humidityVisibility($visibility) {

        $this->humidityVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Luftfeuchte an
     *
     * @return Boolean
     */
    public function isHumidityVisible() {

        return ($this->humidityVisibility == 1 ? true : false);
    }
}