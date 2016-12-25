<?php

namespace SHC\Command\All;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use SHC\Core\SHC;
use SHC\Sensor\SensorPointEditor;

/**
 * Sensordaten Empfangen
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class PushSensorValuesAjax extends AjaxCommand {

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $spId = SHC::getRequest()->getParam('spid', Request::GET, DataTypeUtil::INTEGER);
        $sId = SHC::getRequest()->getParam('sid', Request::GET, DataTypeUtil::STRING);
        $type = SHC::getRequest()->getParam('type', Request::GET, DataTypeUtil::INTEGER);
        $value1 = SHC::getRequest()->getParam('v1', Request::GET, DataTypeUtil::STRING);
        $value2 = SHC::getRequest()->getParam('v2', Request::GET, DataTypeUtil::STRING);
        $value3 = SHC::getRequest()->getParam('v3', Request::GET, DataTypeUtil::STRING);

        if($spId === null || $type === null || $value1 === null) {

            //Fehlende Plichtangabe
            $this->data = 2;
            return;
        }

        if($sId === null) {

            //Fehlende Plichtangabe
            $this->data = 2;
            return;
        }

        switch($type) {

            case SensorPointEditor::SENSOR_DS18X20:

                //Sensor ID pruefen
                if(preg_match('#^(10)|(22)|(28)-[0-9a-f]{6,12}#i', $sId) && $spId >= 1 && $spId <= 998) {

                    //$value1 => Temperatur in °C
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_DS18X20, (float) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_DHT:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998 && $value2 !== null) {

                    //$value1 => Temperatur in °C
                    //$value2 => Luftfeuchte in %
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_DHT, (float) $value1, (float) $value2)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_BMP:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998 && $value2 !== null && $value3 !== null) {

                    //$value1 => Temperatur in °C
                    //$value2 => Luftdruck in hPa
                    //$value3 => Altitude in m
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_BMP, (float) $value1, (float) $value2, (float) $value3)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_RAIN:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Digitalisierter Analogwert (0 - 1023)
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_RAIN, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_HYGROMETER:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Digitalisierter Analogwert (0 - 1023)
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_HYGROMETER, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_LDR:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Digitalisierter Analogwert (0 - 1023)
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_LDR, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_AVM_MEASURING_SOCKET:

                //Sensor ID pruefen
                if($spId == 999 && preg_match('#^[0-9a-fA-F\:\. ]{1,}$#', $sId) && $value2 !== null && $value3 !== null) {

                    //$value1 => Temperatur in °C
                    //$value2 => aktuell entnommene Leistung in mW
                    //$value3 => entnommene Leistung in Wh
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_AVM_MEASURING_SOCKET, (float) $value1, (int) $value2, (int) $value3)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_EDIMAX_MEASURING_SOCKET:

                //Sensor ID pruefen
                if($spId <= 999 && preg_match('#^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$#', $sId) && $value2 !== null) {

                    //$value1 => aktuell entnommene Leistung in mW
                    //$value2 => entnommene Leistung in Wh
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_EDIMAX_MEASURING_SOCKET, (int) $value1, (int) $value2)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_GASMETER:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Gasmenge in ml (wird zum bestehenden Wert addiert)
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_GASMETER, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_WATERMETER:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Wassermenge in ml (wird zum bestehenden Wert addiert)
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_WATERMETER, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_COMET_DECT_RADIATOR_THERMOSTAT:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Temperatur in °C
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_COMET_DECT_RADIATOR_THERMOSTAT, (float) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_SCT_013:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => aktuell entnommene Leistung in mW
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_SCT_013, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case SensorPointEditor::SENSOR_HC_SR04:

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 998 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Abstand in mm
                    if(SensorPointEditor::getInstance()->pushSensorValues($spId, $sId, SensorPointEditor::SENSOR_HC_SR04, (int) $value1)) {

                        //erfolgreich gespeichert
                        $this->data = 1;
                        return;
                    }
                    //Speichern fehlgeschlagen
                }
                //ungültige Sensor ID
                $this->data = 3;
                return;
            case 999:

                //Sensorpunkt Spannung
                if(SensorPointEditor::getInstance()->setSensorPointVoltage($spId, $value1)) {

                    //erfolgreich gespeichert
                    $this->data = 1;
                    return;
                }

                //Speichern fehlgeschlagen
                $this->data = 3;
                return;
            default:

                //ungültiger Sensortyp
                $this->data = 4;
                return;
        }
    }

}