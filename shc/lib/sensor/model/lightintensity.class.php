<?php

namespace SHC\Sensor\Model;

//Imports


/**
 * Lichtstaerke Schnittstelle
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
interface LightIntensity {

    /**
     * gibt die Aktuelle Lichtstaerke zurueck
     *
     * @return Float
     */
    public function getLightIntensity();

    /**
     * gibt die Aktuelle Lichtstaerke vorbereitet zur Anzeige zurueck
     *
     * @return String
     */
    public function getDisplayLightIntensity();

    /**
     * setzt das Lichtstaerke Offset
     *
     * @param  Float $temperatureOffset
     * @return \SHC\Sensor\Model\LightIntensity
     */
    public function setLightIntensityOffset($lightIntensityOffset);

    /**
     * gbit das Lichtstaerke Offset zurueck
     *
     * @return Float
     */
    public function getLightIntensityOffset();

    /**
     * setzt die Sichtbarkeit der Lichtstaerke
     *
     * @param  Integer $visibility Sichtbarkeit
     * @return \SHC\Sensor\Model\LightIntensity
     */
    public function lightIntensityVisibility($visibility);

    /**
     * gibt die Sichtbarkeit der Lichtstaerke an
     *
     * @return Boolean
     */
    public function isLightIntensityVisible();
}