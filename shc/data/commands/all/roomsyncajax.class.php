<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\JSON;
use RWF\Util\String;
use SHC\Room\Room;
use SHC\Room\RoomEditor;
use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\FritzBox;

/**
 * Daten eines Raumes Synchronisieren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomSyncAjax extends AjaxCommand {

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
            if ($switchable->isUserEntitled(RWF::getVisitor()) && $switchable->isEnabled() && $switchable->isVisible()) {

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
        foreach($sensors as $sensor) {

            if($sensor->isVisible()) {

                if ($sensor instanceof DS18x20) {

                    $ds18x20Values[$sensor->getId()] = array(
                        'temp' => String::formatFloat($sensor->getTemperature(), 1)
                    );
                } elseif ($sensor instanceof DHT) {

                    $dhtValues[$sensor->getId()] = array(
                        'temp' => String::formatFloat($sensor->getTemperature(), 1),
                        'hum' => String::formatFloat($sensor->getHumidity(), 1)
                    );
                } elseif ($sensor instanceof BMP) {

                    $bmpValues[$sensor->getId()] = array(
                        'temp' => String::formatFloat($sensor->getTemperature(), 1),
                        'press' => String::formatFloat($sensor->getPressure(), 1),
                        'alti' => String::formatFloat($sensor->getAltitude(), 1)
                    );
                } elseif ($sensor instanceof Hygrometer || $sensor instanceof RainSensor || $sensor instanceof LDR) {

                    $analogValues[$sensor->getId()] = array(
                        'value' => String::formatInteger($sensor->getValue() * 100 / 1023)
                    );
                } elseif ($sensor instanceof AvmMeasuringSocket) {

                    $avmPowerValues[$sensor->getId()] = array(
                        'temp' => String::formatFloat($sensor->getTemperature(), 1),
                        'power' => String::formatFloat(($sensor->getPower() / 1000), 2),
                        'energy' => ($sensor->getEnergy() < 1000 ? String::formatFloat($sensor->getEnergy(), 0) .'Wh' : String::formatFloat(($sensor->getEnergy() / 1000), 3) .'kWh')
                    );
                }
            }
        }

        //Sensoren vorbereiten
        $response['ds18x20'] = $ds18x20Values;
        $response['dht'] = $dhtValues;
        $response['bmp'] = $bmpValues;
        $response['analog'] = $analogValues;
        $response['syncAvmPowerSockect'] = $avmPowerValues;

        $this->data = $response;
    }

}