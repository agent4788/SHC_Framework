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

        switch($type) {

            case SensorPointEditor::SENSOR_DS18X20:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if(preg_match('#^(10)|(22)|(28)-[0-9a-f]{6,12}#i', $sId)) {

                    //$value1 => Temperatur
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
                break;
            case SensorPointEditor::SENSOR_DHT:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 999 && $sId >= 1 && $sId <= 998 && $value2 !== null) {

                    //$value1 => Temperatur
                    //$value2 => Luftfeuchte
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
                break;
            case SensorPointEditor::SENSOR_BMP:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 999 && $sId >= 1 && $sId <= 998 && $value2 !== null && $value3 !== null) {

                    //$value1 => Temperatur
                    //$value2 => Luftdruck
                    //$value3 => Altitude
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
                break;
            case SensorPointEditor::SENSOR_RAIN:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 999 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Digitalisierter Analogwert
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
                break;
            case SensorPointEditor::SENSOR_HYGROMETER:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 999 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Digitalisierter Analogwert
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
                break;
            case SensorPointEditor::SENSOR_LDR:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 999 && $sId >= 1 && $sId <= 998) {

                    //$value1 => Digitalisierter Analogwert
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
                break;
            case SensorPointEditor::SENSOR_AVM_MEASURING_SOCKET:

                if($sId === null) {

                    //Fehlende Plichtangabe
                    $this->data = 2;
                    return;
                }

                //Sensor ID pruefen
                if($spId >= 1 && $spId <= 999 && preg_match('#^[0-9a-fA-F\:\. ]{1,}$#', $sId) && $value2 !== null && $value3 !== null) {

                    //$value1 => Temperatur
                    //$value2 => aktuell entnommene Leistung
                    //$value3 => entnommene Leistung
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
                break;
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
                break;
            default:

                //ungültiger Sensortyp
                $this->data = 4;
                return;
        }
    }

}