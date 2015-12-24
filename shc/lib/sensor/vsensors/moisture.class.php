<?php

namespace SHC\Sensor\vSensors;

//Imports
use RWF\Util\String;
use SHC\Sensor\AbstractSensor;
use SHC\Sensor\vSensor;

/**
 * Vitueller Feuchtigkeits Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Moisture extends AbstractSensor implements vSensor {

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
     * fuegt einen Feuchtigkeitssensor hinzu
     *
     * @param \SHC\Sensor\Model\Temperature $sensor
     */
    public function addSensor(\SHC\Sensor\Model\Moisture $sensor) {

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

                /* @var $sensor \SHC\Sensor\Model\Moisture */
                $val = $sensor->getMoisture();

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
     * gibt die Minimale Feuchtigkeit zurück
     *
     * @return float
     */
    public function getMinMoisture() {

        return $this->min;
    }

    /**
     * gibt die Minimale Feuchtigkeit zur Anzeige zurück
     *
     * @return string
     */
    public function getMinDisplayMoisture() {

        return String::formatInteger($this->getMaxMoisture() * 100 / 1023) .'%';
    }

    /**
     * gibt die Mittlere Feuchtigkeit zurück
     *
     * @return float
     */
    public function getAvarageMoisture() {

        return $this->average;
    }

    /**
     * gibt die Mittlere Feuchtigkeit zur Anzeige zurück
     *
     * @return string
     */
    public function getAvarageDisplayMoisture() {

        return String::formatInteger($this->getAvarageMoisture() * 100 / 1023) .'%';
    }

    /**
     * gibt die Maximale Feuchtigkeit zurück
     *
     * @return float
     */
    public function getMaxMoisture() {

        return $this->max;
    }

    /**
     * gibt die Maximale Feuchtigkeit zur Anzeige zurück
     *
     * @return string
     */
    public function getMaxDisplayMoisture() {

        return String::formatInteger($this->getMaxMoisture() * 100 / 1023) .'%';
    }
}