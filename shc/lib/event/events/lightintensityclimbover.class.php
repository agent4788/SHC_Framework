<?php

namespace SHC\Event\Events;

//Imports
use SHC\Event\AbstractEvent;
use SHC\Sensor\Model\LightIntensity;
use SHC\Sensor\SensorPointEditor;
use RWF\Date\DateTime;

/**
 * Ereignis Lichtstaerke steigt ueber Grenzsert
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LightIntensityClimbOver extends AbstractEvent {

    /**
     * gibt an ob das Ereigniss erfuellt ist
     *
     * @return Boolean
     */
    public function isSatisfies() {

        //Pruefen ob Ereignis aktiv
        if($this->enabled == false) {

            return false;
        }

        //noetige Parameter pruefen
        if (!isset($this->data['sensors'])) {

            throw new \Exception('Eine Liste mit Temperatursensoren muss angegeben werden', 1580);
        }
        $this->data['sensors'] = explode(',', $this->data['sensors']);
        if (!isset($this->data['limit'])) {

            throw new \Exception('Es muss ein Grenzwert angegeben werden', 1580);
        }

        //pruefen ob Warteintervall angegeben und noch nicht abgelaufen
        if(isset($this->data['interval'])) {

            if($this->time instanceof DateTime){

                $date = clone $this->time;
                $date->add(new \DateInterval('PT'. $this->data['interval'] .'S'));
                if($date->isFuture()) {

                    //noch in der Sperrzeit fuer weitere Events
                    return false;
                }
            }
        }

        //Limit vorbereiten (aus % einen Digitalwert errechnen
        $limit = round(1023 * $this->data['limit'], 0);

        //pruefen ob der Ereigniszustand erfuellt ist
        $success = false;
        $sensors = SensorPointEditor::getInstance()->listSensors(SensorPointEditor::SORT_NOTHING);
        foreach($sensors as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensors\LDR */
            if(in_array($sensor->getId(), $this->data['sensors']) && $sensor instanceof LightIntensity) {

                if(isset($this->state[$sensor->getId()])) {

                    if($this->state[$sensor->getId()] < $limit && $sensor->getLightIntensity() >= $limit) {

                        //Sensor bekannt, Sensorwert ist ueber Grenzwert gestiegen
                        $this->state[$sensor->getId()] = $sensor->getLightIntensity();
                        $success = true;
                    } else {

                        //Sensor bekannt, Grenzwert aber nicht ueberschritten
                        $this->state[$sensor->getId()] = $sensor->getLightIntensity();
                    }
                } else {

                    //Sensor unbekannt => registrieren und aktuellen Sensorwert speichern
                    $this->state[$sensor->getId()] = $sensor->getLightIntensity();
                }
            }
        }

        //kein Zustandswechsel erfolgt
        if($success === false) {

            return false;
        }

        //Bedingungen pruefen
        foreach ($this->conditions as $condition) {

            /* @var $condition \SHC\Condition\Condition */
            if(!$condition->isSatisfies()) {

                //eine Bedingung trifft nicht zu
                return false;
            }
        }

        //Ereignis zur ausfuehrung bereit
        $this->time = DateTime::now();
        return true;
    }

}