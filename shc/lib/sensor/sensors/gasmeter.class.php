<?php

namespace SHC\Sensor\Sensors;

//Imports
use SHC\Sensor\AbstractSensor;
use RWF\Date\DateTime;
use SHC\Sensor\Model\AbstractFluidAmount;
use SHC\Sensor\Model\FluidAmount;

/**
 * Wasser/Gas-Zaehler
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class GasMeter extends AbstractSensor implements FluidAmount {

    use AbstractFluidAmount;

    /**
     * @param Array  $values   Sensorwerte
     */
    public function __construct(array $values = array()) {

        if(count($values) <= 25) {

            $this->oldValues = $values;
            $this->fluidAmount = $values[0]['amount'];
            $this->time = $values[0]['time'];
        }
    }

    /**
     * setzt den aktuellen Sensorwert und schiebt ih in das Werte Array
     *
     * @param Float $fluidAmount Flüssigkeitsmenge
     */
    public function pushValues($fluidAmount) {

        $date = DateTime::now();

        //alte Werte Schieben
        array_unshift($this->oldValues, array('amount' => $fluidAmount, 'time' => $date));
        //mehr als 5 Werte im Array?
        if(isset($this->oldValues[25])) {

            //aeltesten Wert loeschen
            unset($this->oldValues[25]);
        }

        //Werte setzten
        $this->fluidAmount = $fluidAmount;
        $this->time = $date;
        $this->isModified = true;
    }
}