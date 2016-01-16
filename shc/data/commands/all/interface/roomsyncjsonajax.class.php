<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\User\UserEditor;
use RWF\Util\DataTypeUtil;
use SHC\Room\Room;
use SHC\Room\RoomEditor;
use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\CometDectRadiatorThermostat;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\EdimaxMeasuringSocket;
use SHC\Sensor\Sensors\GasMeter;
use SHC\Sensor\Sensors\HcSr04;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Sensor\Sensors\SCT013;
use SHC\Sensor\Sensors\WaterMeter;
use SHC\Sensor\vSensors\Energy;
use SHC\Sensor\vSensors\FluidAmount;
use SHC\Sensor\vSensors\Humidity;
use SHC\Sensor\vSensors\LightIntensity;
use SHC\Sensor\vSensors\Moisture;
use SHC\Sensor\vSensors\Power;
use SHC\Sensor\vSensors\Temperature;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * Daten eines Raumes Synchronisieren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomSyncJsonAjax extends AjaxCommand {

    protected $premission = '';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array();

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Raum Objekt laden
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $room = RoomEditor::getInstance()->getRoomById($roomId);

        //pruefen ob der Raum existiert
        if(!$room instanceof Room) {

            $this->data = array(
                'success' => false,
                'message' => 'ungültige Raum Id'
            );
            return;
        }

        //Benutzeranmeldung
        $rwfUser = UserEditor::getInstance()->getGuest();
        if(RWF::getRequest()->issetParam('user', Request::GET) && RWF::getRequest()->issetParam('password', Request::GET)) {

            $userName = RWF::getRequest()->getParam('user', Request::GET, DataTypeUtil::PLAIN);
            $password = RWF::getRequest()->getParam('password', Request::GET, DataTypeUtil::PLAIN);

            $user = UserEditor::getInstance()->getUserByName($userName);
            if($user != null && $user->checkPasswordHash($password)) {

                $rwfUser = $user;
            }
        }

        //Antwort vorbereiten
        $response = array();
        $response['success'] = true;

        //schaltbare und lesbare Elemente
        SwitchableEditor::getInstance()->loadData();
        $switchables = SwitchableEditor::getInstance()->listElementsForRoom($roomId, SwitchableEditor::SORT_NOTHING);
        $switchableValues = array();
        $wolValues = array();
        $readableValues = array();
        foreach ($switchables as $switchable) {

            //Berechtigungen pruefen
            if ($switchable->isUserEntitled($rwfUser) && $switchable->isEnabled() && $switchable->isVisible()) {

                if ($switchable instanceof WakeOnLan) {

                    $wolValues[$switchable->getId()] = $switchable->getState();
                } elseif ($switchable instanceof Switchable) {

                    if($switchable instanceof Script && $switchable->getOnCommand() != '' && $switchable->getOffCommand() != '') {

                        $switchableValues[$switchable->getId()] = $switchable->getState();
                    } elseif($switchable instanceof FritzBox) {

                        if($switchable->getFunction() <= 3) {

                            $switchableValues[$switchable->getId()] = $switchable->getState();
                        }
                    } elseif(!$switchable instanceof Script) {

                        $switchableValues[$switchable->getId()] = $switchable->getState();
                    }
                } elseif ($switchable instanceof Readable) {

                    //Status lesen ohne ihn zu speichern
                    $readableValues[$switchable->getId()] = $switchable->getState();
                }
            }
        }

        //schaltbare Elemente zum senden vorbereiten
        $response['switchables'] = $switchableValues;
        $response['wol'] = $wolValues;
        $response['readables'] = $readableValues;

        //Sensoren
        SensorPointEditor::getInstance()->loadData();
        $sensors = SensorPointEditor::getInstance()->listSensorsForRoom($roomId, SensorPointEditor::SORT_NOTHING);
        $ds18x20Values = array();
        $dhtValues = array();
        $bmpValues = array();
        $analogValues = array();
        $avmPowerValues = array();
        $fluidAmountValues = array();
        $cometThermostatValues = array();
        $edimaxPowerValues = array();
        $sctPowerValues = array();
        $distanceValues = array();
        $vEnergyValues = array();
        $vAmountValues = array();
        $vHumidityValues = array();
        $vLightIntensityValues = array();
        $vMoistureValues = array();
        $vPowerValues = array();
        $vTemaratureValues = array();
        foreach($sensors as $sensor) {

            if($sensor->isVisible()) {

                if ($sensor instanceof DS18x20) {

                    $ds18x20Values[$sensor->getId()] = array(
                        'temp' => $sensor->getDisplayTemperature()
                    );
                } elseif ($sensor instanceof DHT) {

                    $dhtValues[$sensor->getId()] = array(
                        'temp' => $sensor->getDisplayTemperature(),
                        'hum' => $sensor->getDisplayHumidity()
                    );
                } elseif ($sensor instanceof BMP) {

                    $bmpValues[$sensor->getId()] = array(
                        'temp' => $sensor->getDisplayTemperature(),
                        'press' => $sensor->getDisplayAirPressure(),
                        'alti' => $sensor->getDisplayAltitude()
                    );
                } elseif ($sensor instanceof Hygrometer || $sensor instanceof RainSensor) {

                    $analogValues[$sensor->getId()] = array(
                        'value' => $sensor->getDisplayMoisture()
                    );
                } elseif ($sensor instanceof LDR) {

                    $analogValues[$sensor->getId()] = array(
                        'value' => $sensor->getDisplayLightIntensity()
                    );
                } elseif ($sensor instanceof AvmMeasuringSocket) {

                    $avmPowerValues[str_replace(' ', '-', $sensor->getId())] = array(
                        'temp' => $sensor->getDisplayTemperature(),
                        'power' => $sensor->getDisplayPower(),
                        'energy' => $sensor->getDisplayEnergy()
                    );
                } elseif ($sensor instanceof GasMeter || $sensor instanceof WaterMeter) {

                    $fluidAmountValues[$sensor->getId()] = array(
                        'amount' => $sensor->getDisplayFluidAmount()
                    );
                } elseif ($sensor instanceof CometDectRadiatorThermostat) {

                    $cometThermostatValues[$sensor->getId()] = array(
                        'temp' => $sensor->getDisplayTemperature()
                    );
                } elseif ($sensor instanceof EdimaxMeasuringSocket) {

                    $edimaxPowerValues[str_replace('.', '_', $sensor->getId())] = array(
                        'power' => $sensor->getDisplayPower(),
                        'energy' => $sensor->getDisplayEnergy()
                    );
                } elseif ($sensor instanceof SCT013) {

                    $sctPowerValues[$sensor->getId()] = array(
                        'power' => $sensor->getDisplayPower()
                    );
                } elseif ($sensor instanceof HcSr04) {

                    $distanceValues[$sensor->getId()] = array(
                        'dist' => $sensor->getDisplayDistance()
                    );
                } elseif ($sensor instanceof Energy) {

                    $vEnergyValues[$sensor->getId()] = array(
                        'sum' => $sensor->getSumDisplayEnergy()
                    );
                } elseif ($sensor instanceof FluidAmount) {

                    $vAmountValues[$sensor->getId()] = array(
                        'sum' => $sensor->getSumDisplayFluidAmount()
                    );
                } elseif ($sensor instanceof Humidity) {

                    $vHumidityValues[$sensor->getId()] = array(
                        'min' => $sensor->getMinDisplayHunidity(),
                        'avg' => $sensor->getAvarageDisplayHunidity(),
                        'max' => $sensor->getMaxDisplayHunidity()
                    );
                } elseif ($sensor instanceof LightIntensity) {

                    $vLightIntensityValues[$sensor->getId()] = array(
                        'min' => $sensor->getMinDisplayLightIntensity(),
                        'avg' => $sensor->getAvarageDisplayLightIntensity(),
                        'max' => $sensor->getMaxLightIntensity()
                    );
                } elseif ($sensor instanceof Moisture) {

                    $vMoistureValues[$sensor->getId()] = array(
                        'min' => $sensor->getMaxDisplayMoisture(),
                        'avg' => $sensor->getAvarageDisplayMoisture(),
                        'max' => $sensor->getMaxDisplayMoisture()
                    );
                } elseif ($sensor instanceof Power) {

                    $vPowerValues[$sensor->getId()] = array(
                        'avg' => $sensor->getAvarageDisplayPower(),
                        'sum' => $sensor->getSumDisplayPower()
                    );
                } elseif ($sensor instanceof Temperature) {

                    $vTemaratureValues[$sensor->getId()] = array(
                        'min' => $sensor->getMinTemperature(),
                        'avg' => $sensor->getAvarageTemperature(),
                        'max' => $sensor->getMaxTemperature()
                    );
                }
            }
        }

        //Sensoren vorbereiten
        $response['ds18x20'] = $ds18x20Values;
        $response['dht'] = $dhtValues;
        $response['bmp'] = $bmpValues;
        $response['analog'] = $analogValues;
        $response['syncAvmPowerSocket'] = $avmPowerValues;
        $response['syncFluidAmountMeters'] = $fluidAmountValues;
        $response['syncCometThermostat'] = $cometThermostatValues;
        $response['syncEdimaxPowerSocket'] = $edimaxPowerValues;
        $response['syncSctPower'] = $sctPowerValues;
        $response['syncDistance'] = $distanceValues;
        $response['syncvEnergy'] = $vEnergyValues;
        $response['syncvAmount'] = $vAmountValues;
        $response['syncvHumidity'] = $vHumidityValues;
        $response['syncvLightIntensity'] = $vLightIntensityValues;
        $response['syncvMoisture'] = $vMoistureValues;
        $response['syncvPower'] = $vPowerValues;
        $response['syncvTemperature'] = $vTemaratureValues;

        $this->data = $response;
    }

}