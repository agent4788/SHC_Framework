<?php

namespace SHC\Sensor\vSensors;

//Imports
use RWF\Util\String;
use SHC\Sensor\AbstractSensor;
use SHC\Sensor\vSensor;

/**
 * Vitueller MengenMesser Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class FluidAmount extends AbstractSensor implements vSensor {

    /**
     * Liste der Sensoren
     *
     * @var array
     */
    protected $sensors = array();

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
     * fuegt einen Mengensensor hinzu
     *
     * @param \SHC\Sensor\Model\FluidAmount $sensor
     */
    public function addSensor(\SHC\Sensor\Model\FluidAmount $sensor) {

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

            $sum = 0.0;
            foreach($this->sensors as $sensor) {

                /* @var $sensor \SHC\Sensor\Model\FluidAmount */
                $val = $sensor->getFluidAmount();

                //Mittelwert
                $sum += $val;
            }

            //Summe
            $this->sum = $sum;
        }
    }

    /**
     * gibt die gesamte Menge zurück
     *
     * @return float
     */
    public function getSumFluidAmount() {

        return $this->sum;
    }

    /**
     * gibt die gesamte Menge zur Anzeige zurück
     *
     * @return string
     */
    public function getSumDisplayFluidAmount() {

        if($this->getSumFluidAmount() >= 1000000) {

            return String::formatFloat($this->getSumFluidAmount() / 1000000, 1) .'m³';
        } elseif($this->getSumFluidAmount() >= 1000) {

            return String::formatFloat($this->getSumFluidAmount() / 1000, 1) .'l';
        }
        return String::formatFloat($this->getSumFluidAmount(), 1) .'ml';
    }
}