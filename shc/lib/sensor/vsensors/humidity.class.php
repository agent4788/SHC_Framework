<?php

namespace SHC\Sensor\vSensors;

//Imports
use RWF\Util\String;
use SHC\Sensor\AbstractSensor;
use SHC\Sensor\vSensor;

/**
 * Vitueller Luftfeuchtigkeits Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Humidity extends AbstractSensor implements vSensor {

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
     * fuegt einen Luftfeuchte Sensor hinzu
     *
     * @param \SHC\Sensor\Model\Humidity $sensor
     */
    public function addSensor(\SHC\Sensor\Model\Humidity $sensor) {

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

                /* @var $sensor \SHC\Sensor\Model\Humidity */
                $val = $sensor->getHumidity();

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
     * gibt die Minimale Luftfeuchte zurück
     *
     * @return float
     */
    public function getMinHunidity() {

        return $this->min;
    }

    /**
     * gibt die Minimale Luftfeuchte zur Anzeige zurück
     *
     * @return string
     */
    public function getMinDisplayHunidity() {

        return String::formatFloat($this->getMinHunidity(), 1) .'%';
    }

    /**
     * gibt die Mittlere Luftfeuchte zurück
     *
     * @return float
     */
    public function getAvarageHunidity() {

        return $this->average;
    }

    /**
     * gibt die Mittlere Luftfeuchte zur Anzeige zurück
     *
     * @return string
     */
    public function getAvarageDisplayHunidity() {

        return String::formatFloat($this->getAvarageHunidity(), 1) .'%';
    }

    /**
     * gibt die Maximale Luftfeuchte zurück
     *
     * @return float
     */
    public function getMaxHunidity() {

        return $this->max;
    }

    /**
     * gibt die Maximale Luftfeuchte zur Anzeige zurück
     *
     * @return string
     */
    public function getMaxDisplayHunidity() {

        return String::formatFloat($this->getMaxHunidity(), 1) .'%';
    }
}