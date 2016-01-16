<?php

namespace SHC\Sensor\vSensors;

//Imports
use RWF\Util\String;
use SHC\Sensor\AbstractSensor;
use SHC\Sensor\vSensor;

/**
 * Vitueller Lichtstärke Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LightIntensity extends AbstractSensor implements vSensor {

    /**
     * Liste der Sensoren
     *
     * @var array
     */
    protected $sensors = array();

    /**
     * Minimalwert
     *
     * @var float
     */
    protected $min = 0.0;

    /**
     * Mittelwert
     *
     * @var float
     */
    protected $average = 0.0;

    /**
     * Maximalwert
     *
     * @var float
     */
    protected $max = 0.0;

    /**
     * @param array $sensors Sensoren
     */
    public function __construct(array $sensors = array()) {

        foreach($sensors as $sensor) {

            $this->addSensor($sensor);
        }
        $this->processData();
    }

    /**
     * fuegt einen Lichstärke hinzu
     *
     * @param \SHC\Sensor\Model\LightIntensity $sensor
     */
    public function addSensor(\SHC\Sensor\Model\LightIntensity $sensor) {

        $this->sensors[] = $sensor;
    }

    /**
     * gibt eine Liste mit den IDs der Sensoren zurück
     *
     * @return mixed
     */
    public function listSensorIDs() {

        $list = array();
        foreach($this->sensors as $sensor) {

            $list[] = $sensor->getId();
        }
        return $list;
    }

    /**
     * verarbeitet die Daten der einzelnen Sensoren
     */
    public function processData() {

        if(count($this->sensors) >= 1) {

            $this->min = null;
            $this->average = null;
            $this->max = null;
            $sum = 0.0;
            $count = 0;
            foreach($this->sensors as $sensor) {

                /* @var $sensor \SHC\Sensor\Model\LightIntensity */
                $val = $sensor->getLightIntensity();

                //Minimalwert
                if($val < $this->min || $this->min === null) {

                    $this->min = $val;
                }

                //Mittelwert
                $sum += $val;
                $count++;

                //Maximalwert
                if($val > $this->max || $this->max === null) {

                    $this->max = $val;
                }
            }

            //Mittelwert auswerten
            $this->average = ($sum / $count);
        }
    }

    /**
     * gibt die Minimale Lichtstärke zurück
     *
     * @return float
     */
    public function getMinLightIntensity() {

        return $this->min;
    }

    /**
     * gibt die Minimale Lichtstärke zur Anzeige zurück
     *
     * @return string
     */
    public function getMinDisplayLightIntensity() {

        return String::formatInteger($this->getMaxLightIntensity() * 100 / 1023) .'%';
    }

    /**
     * gibt die Mittlere Lichtstärke zurück
     *
     * @return float
     */
    public function getAvarageLightIntensity() {

        return $this->average;
    }

    /**
     * gibt die Mittlere Lichtstärke zur Anzeige zurück
     *
     * @return string
     */
    public function getAvarageDisplayLightIntensity() {

        return String::formatInteger($this->getAvarageLightIntensity() * 100 / 1023) .'%';
    }

    /**
     * gibt die Maximale Lichtstärke zurück
     *
     * @return float
     */
    public function getMaxLightIntensity() {

        return $this->max;
    }

    /**
     * gibt die Maximale Lichtstärke zur Anzeige zurück
     *
     * @return string
     */
    public function getMaxDisplayLightIntensity() {

        return String::formatInteger($this->getMaxLightIntensity() * 100 / 1023) .'%';
    }
}