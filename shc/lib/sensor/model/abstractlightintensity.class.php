<?php

namespace SHC\Sensor\Model;

//Imports
use RWF\Util\String;

/**
 * Lichtstaerke Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
trait AbstractLightIntensity {

    /**
     * Lichtstaerke
     *
     * @var Integer
     */
    protected $lightIntensity = 0;

    /**
     * Lichtstaerke Anzeigen
     *
     * @var Integer
     */
    protected $lightIntensityVisibility = 1;

    /**
     * Lichtstaerke Offset
     *
     * @var Integer
     */
    protected $lightIntensityOffset = 0;

    /**
     * gibt die Aktuelle Lichtstaerke zurueck
     *
     * @return Float
     */
    public function getLightIntensity() {

        return $this->lightIntensity + $this->lightIntensityOffset;
    }

    /**
     * gibt die Aktuelle Lichtstaerke vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayLightIntensity() {

        return String::formatInteger($this->getLightIntensity() * 100 / 1023) .'%';
    }

    /**
     * setzt das Lichtstaerke Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Model\LightIntensity
     */
    public function setLightIntensityOffset($lightIntensityOffset) {

        $this->lightIntensityOffset = $lightIntensityOffset;
        return $this;
    }

    /**
     * gbit das Lichtstaerke Offset zurueck
     *
     * @return Float
     */
    public function getLightIntensityOffset() {

        return $this->lightIntensityOffset;
    }

    /**
     * setzt die Sichtbarkeit der Lichtstaerke
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\LightIntensity
     */
    public function lightIntensityVisibility($visibility) {

        $this->lightIntensityVisibility = $visibility;
        return $this;
    }

    /**
     * gibt die Sichtbarkeit der Lichtstaerke an
     *
     * @return Boolean
     */
    public function isLightIntensityVisible() {

        return ($this->lightIntensityVisibility == 1 ? true : false);
    }
}