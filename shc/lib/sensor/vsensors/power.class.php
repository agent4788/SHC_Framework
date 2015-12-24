<?php

namespace SHC\Sensor\vSensors;

//Imports
use RWF\Util\String;
use SHC\Sensor\AbstractSensor;
use SHC\Sensor\vSensor;

/**
 * Vitueller Stromverbrauchs Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Power extends AbstractSensor implements vSensor {

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
     * Summe
     *
     * @var float
     */
    protected $sum = 0.0;

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
     * fuegt einen Stromverbrauchs Sensor hinzu
     *
     * @param \SHC\Sensor\Model\Power $sensor
     */
    public function addSensor(\SHC\Sensor\Model\Power $sensor) {

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
            $this->sum = null;
            $sum = 0.0;
            $count = 0;
            foreach($this->sensors as $sensor) {

                /* @var $sensor \SHC\Sensor\Model\Power */
                $val = $sensor->getPower();

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

            //Summe
            $this->sum = $sum;
        }
    }

    /**
     * gibt den Minimalen Stromverbrauch zurück
     *
     * @return float
     */
    public function getMinPower() {

        return $this->min;
    }

    /**
     * gibt den Minimalen Stromverbrauch zur Anzeige zurück
     *
     * @return string
     */
    public function getMinDisplayPower() {

        return ($this->getMinPower() < 1000000.0 ? String::formatFloat($this->getMinPower() / 1000, 1) .' W' : String::formatFloat($this->getMinPower() / 1000 / 1000, 1) .' kW');
    }

    /**
     * gibt den Mittleren Stromverbrauch zurück
     *
     * @return float
     */
    public function getAvaragePower() {

        return $this->average;
    }

    /**
     * gibt den Mittleren Stromverbrauch zur Anzeige zurück
     *
     * @return string
     */
    public function getAvarageDisplayPower() {

        return ($this->getAvaragePower() < 1000000.0 ? String::formatFloat($this->getAvaragePower() / 1000, 1) .' W' : String::formatFloat($this->getAvaragePower() / 1000 / 1000, 1) .' kW');
    }

    /**
     * gibt den Maximalen Stromverbrauch zurück
     *
     * @return float
     */
    public function getMaxPower() {

        return $this->max;
    }

    /**
     * gibt den Maximalen Stromverbrauch Anzeige zurück
     *
     * @return string
     */
    public function getMaxDisplayPower() {

        return ($this->getMaxPower() < 1000000.0 ? String::formatFloat($this->getMaxPower() / 1000, 1) .' W' : String::formatFloat($this->getMaxPower() / 1000 / 1000, 1) .' kW');
    }

    /**
     * gibt den gesamten Stromverbrauch zurück
     *
     * @return float
     */
    public function getSumPower() {

        return $this->sum;
    }

    /**
     * gibt den gesamten Stromverbrauch zur Anzeige zurück
     *
     * @return string
     */
    public function getSumDisplayPower() {

        return ($this->getSumPower() < 1000000.0 ? String::formatFloat($this->getSumPower() / 1000, 1) .' W' : String::formatFloat($this->getSumPower() / 1000 / 1000, 1) .' kW');
    }
}