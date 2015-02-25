<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Form\Forms\ActivityForm;
use SHC\Form\Forms\BMPSensorForm;
use SHC\Form\Forms\DHTSensorForm;
use SHC\Form\Forms\DS18x20SensorForm;
use SHC\Form\Forms\HygrometerSensorForm;
use SHC\Form\Forms\LDRSensorForm;
use SHC\Form\Forms\RainSensorForm;
use SHC\Room\RoomEditor;
use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Switchable\SwitchableEditor;


/**
 * bearbeitet einen Sensor
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSensorFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'acpindex', 'form');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Sensor Objekt laden
        $sensorId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::STRING);
        $sensor = SensorPointEditor::getInstance()->getSensorById($sensorId);

        //Formulare je nach Objekttyp erstellen
        if($sensor instanceof BMP) {

            //BMP Sensor
            $bmpSensorForm = new BMPSensorForm($sensor);
            $bmpSensorForm->addId('shc-view-form-editSensor');

            if($bmpSensorForm->isSubmitted() && $bmpSensorForm->validate()) {

                //Speichern
                $name = $bmpSensorForm->getElementByName('name')->getValue();
                $roomId = $bmpSensorForm->getElementByName('room')->getValue();
                $visibility = $bmpSensorForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $bmpSensorForm->getElementByName('temperatureVisibility')->getValue();
                $pressureVisibility = $bmpSensorForm->getElementByName('pressureVisibility')->getValue();
                $altitudeVisibility = $bmpSensorForm->getElementByName('altitudeVisibility')->getValue();
                $temperatureOffset = $bmpSensorForm->getElementByName('tempOffset')->getValue();
                $pressureOffset = $bmpSensorForm->getElementByName('pressOffset')->getValue();
                $altitudeOffset = $bmpSensorForm->getElementByName('altiOffset')->getValue();
                $dataRecording = $bmpSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editBMP($sensorId, $name, $roomId, null, $visibility, $temperatureVisibility, $pressureVisibility, $altitudeVisibility, $dataRecording, $temperatureOffset, $pressureOffset, $altitudeOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1507) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1507'));
                    } elseif($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                $tpl->assign('message', $message);
            } else {

                //Formular anzeigen
                $tpl->assign('sensor', $sensor);
                $tpl->assign('elementForm', $bmpSensorForm);
            }
        } elseif($sensor instanceof DHT) {

            //DHT Sensor
            $dhtSensorForm = new DHTSensorForm($sensor);
            $dhtSensorForm->addId('shc-view-form-editSensor');

            if($dhtSensorForm->isSubmitted() && $dhtSensorForm->validate()) {

                //Speichern
                $name = $dhtSensorForm->getElementByName('name')->getValue();
                $roomId = $dhtSensorForm->getElementByName('room')->getValue();
                $visibility = $dhtSensorForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $dhtSensorForm->getElementByName('temperatureVisibility')->getValue();
                $humidityVisibility = $dhtSensorForm->getElementByName('humidityVisibility')->getValue();
                $temperatureOffset = $dhtSensorForm->getElementByName('tempOffset')->getValue();
                $humidityOffset = $dhtSensorForm->getElementByName('humOffset')->getValue();
                $dataRecording = $dhtSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editDHT($sensorId, $name, $roomId, null, $visibility, $temperatureVisibility, $humidityVisibility, $dataRecording, $temperatureOffset, $humidityOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1507) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1507'));
                    } elseif($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                $tpl->assign('message', $message);
            } else {

                //Formular anzeigen
                $tpl->assign('sensor', $sensor);
                $tpl->assign('elementForm', $dhtSensorForm);
            }
        } elseif($sensor instanceof DS18x20) {

            //DHT Sensor
            $ds18x20SensorForm = new DS18x20SensorForm($sensor);
            $ds18x20SensorForm->addId('shc-view-form-editSensor');

            if($ds18x20SensorForm->isSubmitted() && $ds18x20SensorForm->validate()) {

                //Speichern
                $name = $ds18x20SensorForm->getElementByName('name')->getValue();
                $roomId = $ds18x20SensorForm->getElementByName('room')->getValue();
                $visibility = $ds18x20SensorForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $ds18x20SensorForm->getElementByName('temperatureVisibility')->getValue();
                $temperatureOffset = $ds18x20SensorForm->getElementByName('tempOffset')->getValue();
                $dataRecording = $ds18x20SensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editDS18x20($sensorId, $name, $roomId, null, $visibility, $temperatureVisibility, $dataRecording, $temperatureOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1507) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1507'));
                    } elseif($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                $tpl->assign('message', $message);
            } else {

                //Formular anzeigen
                $tpl->assign('sensor', $sensor);
                $tpl->assign('elementForm', $ds18x20SensorForm);
            }
        } elseif($sensor instanceof Hygrometer) {

            //Hygrometer Sensor
            $hygrometerSensorForm = new HygrometerSensorForm($sensor);
            $hygrometerSensorForm->addId('shc-view-form-editSensor');

            if($hygrometerSensorForm->isSubmitted() && $hygrometerSensorForm->validate()) {

                //Speichern
                $name = $hygrometerSensorForm->getElementByName('name')->getValue();
                $roomId = $hygrometerSensorForm->getElementByName('room')->getValue();
                $visibility = $hygrometerSensorForm->getElementByName('visibility')->getValue();
                $valueVisibility = $hygrometerSensorForm->getElementByName('valueVisibility')->getValue();
                $valueOffset = $hygrometerSensorForm->getElementByName('valOffset')->getValue();
                $dataRecording = $hygrometerSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editHygrometer($sensorId, $name, $roomId, null, $visibility, $valueVisibility, $dataRecording, $valueOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1507) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1507'));
                    } elseif($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                $tpl->assign('message', $message);
            } else {

                //Formular anzeigen
                $tpl->assign('sensor', $sensor);
                $tpl->assign('elementForm', $hygrometerSensorForm);
            }
        } elseif($sensor instanceof RainSensor) {

            //Regensensor
            $rainSensorForm = new RainSensorForm($sensor);
            $rainSensorForm->addId('shc-view-form-editSensor');

            if($rainSensorForm->isSubmitted() && $rainSensorForm->validate()) {

                //Speichern
                $name = $rainSensorForm->getElementByName('name')->getValue();
                $roomId = $rainSensorForm->getElementByName('room')->getValue();
                $visibility = $rainSensorForm->getElementByName('visibility')->getValue();
                $valueVisibility = $rainSensorForm->getElementByName('valueVisibility')->getValue();
                $valueOffset = $hygrometerSensorForm->getElementByName('valOffset')->getValue();
                $dataRecording = $rainSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editRainSensor($sensorId, $name, $roomId, null, $visibility, $valueVisibility, $dataRecording, $valueOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1507) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1507'));
                    } elseif($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                $tpl->assign('message', $message);
            } else {

                //Formular anzeigen
                $tpl->assign('sensor', $sensor);
                $tpl->assign('elementForm', $rainSensorForm);
            }
        } elseif($sensor instanceof LDR) {

            //Lichtsensor
            $ldrSensorForm = new LDRSensorForm($sensor);
            $ldrSensorForm->addId('shc-view-form-editSensor');

            if($ldrSensorForm->isSubmitted() && $ldrSensorForm->validate()) {

                //Speichern
                $name = $ldrSensorForm->getElementByName('name')->getValue();
                $roomId = $ldrSensorForm->getElementByName('room')->getValue();
                $visibility = $ldrSensorForm->getElementByName('visibility')->getValue();
                $valueVisibility = $ldrSensorForm->getElementByName('valueVisibility')->getValue();
                $valueOffset = $hygrometerSensorForm->getElementByName('valOffset')->getValue();
                $dataRecording = $ldrSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editLDR($sensorId, $name, $roomId, null, $visibility, $valueVisibility, $dataRecording, $valueOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1507) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1507'));
                    } elseif($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                $tpl->assign('message', $message);
            } else {

                //Formular anzeigen
                $tpl->assign('sensor', $sensor);
                $tpl->assign('elementForm', $ldrSensorForm);
            }
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            $this->data = $tpl->fetchString('editsensorform.html');
            return;
        }

        //Template ausgeben
        $this->data = $tpl->fetchString('editsensorform.html');
    }

}