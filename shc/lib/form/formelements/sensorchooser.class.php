<?php

namespace SHC\Form\FormElements;

//Imports
use RWF\Form\FormElements\SelectMultiple;
use SHC\Sensor\Model\AirPressure;
use SHC\Sensor\Model\Altitude;
use SHC\Sensor\Model\Humidity;
use SHC\Sensor\Model\LightIntensity;
use SHC\Sensor\Model\Moisture;
use SHC\Sensor\Model\Temperature;
use SHC\Sensor\SensorPointEditor;

/**
 * Sensoren auswahl
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SensorChooser extends SelectMultiple {

    /**
     * alle Sensoren
     *
     * @var Integer
     */
    const ALL = 1;

    /**
     * Temperatur Sensoren
     *
     * @var Integer
     */
    const TEMPERATURE = 2;

    /**
     * Luftfeuchte Sensoren
     *
     * @var Integer
     */
    const HUMDITY = 4;

    /**
     * Luftdruck Sensoren
     *
     * @var Integer
     */
    const PRESSURE = 8;

    /**
     * Hoehen Sensoren
     *
     * @var Integer
     */
    const ALTITUDE = 16;

    /**
     * Feuchtigkeits Sensoren
     *
     * @var Integer
     */
    const MOISTURE = 32;

    /**
     * Lichtstaerke Sensoren
     *
     * @var Integer
     */
    const LINGTH_INTENSIVITY = 64;

    /**
     * @param String  $name      Feld Name
     * @param Array   $sensors   Ausgewaehlte IDs
     * @param Integer $filter    Filter nach denen die Sensoren selektiert werden
     */
    public function __construct($name, $sensors = array(), $filter = self::ALL) {

        $this->setName($name);
        $this->setOptions(array(
            'size' => 10
        ));

        //Daten laden
        $values = array();
        $sensorList = SensorPointEditor::getInstance()->listSensors(SensorPointEditor::SORT_BY_NAME);
        foreach($sensorList as $sensor) {

            /* @var $sensor \SHC\Sensor\Sensor */
            if($filter & self::ALL) {

                //alle Sensoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            } elseif($filter & self::TEMPERATURE && $sensor instanceof Temperature) {

                //Temperatursenoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            } elseif($filter & self::HUMDITY && $sensor instanceof Humidity) {

                //Luftfeuchte Sensoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            } elseif($filter & self::PRESSURE && $sensor instanceof AirPressure) {

                //Luftdrucksensoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            } elseif($filter & self::ALTITUDE && $sensor instanceof Altitude) {

                //Hoehen Sensoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            } elseif($filter & self::MOISTURE && $sensor instanceof Moisture) {

                //Feuchtigkeitssensoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            } elseif($filter & self::LINGTH_INTENSIVITY && $sensor instanceof LightIntensity) {

                //Feuchtigkeitssensoren
                $values[$sensor->getId()] = array($sensor->getName() .' ['. $sensor->getNamedRoomList(true) .']', (in_array($sensor->getId(), $sensors) ? 1 : 0));
                continue;
            }

        }
        $this->setValues($values);
    }
}