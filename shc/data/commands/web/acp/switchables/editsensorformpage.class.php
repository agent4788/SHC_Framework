<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\Sensors\AvmMeasuringSocketForm;
use SHC\Form\Forms\Sensors\BMPSensorForm;
use SHC\Form\Forms\Sensors\DHTSensorForm;
use SHC\Form\Forms\Sensors\DS18x20SensorForm;
use SHC\Form\Forms\Sensors\HygrometerSensorForm;
use SHC\Form\Forms\Sensors\LDRSensorForm;
use SHC\Form\Forms\Sensors\RainSensorForm;
use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSensorFormPage extends PageCommand {

    protected $template = 'sensorform.html';

    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //Sensor Objekt laden
        $sensorId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::STRING);
        $sensor = SensorPointEditor::getInstance()->getSensorById($sensorId);

        //Formulare je nach Objekttyp erstellen
        if($sensor instanceof BMP) {

            //BMP Sensor
            $bmpSensorForm = new BMPSensorForm($sensor);
            $bmpSensorForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $bmpSensorForm->addId('shc-view-form-editSensor');

            if($bmpSensorForm->isSubmitted() && $bmpSensorForm->validate()) {

                //Speichern
                $name = $bmpSensorForm->getElementByName('name')->getValue();
                $icon = $bmpSensorForm->getElementByName('icon')->getValue();
                $rooms = $bmpSensorForm->getElementByName('rooms')->getValues();
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

                    SensorPointEditor::getInstance()->editBMP($sensorId, $name, $icon, $rooms, null, $visibility, $temperatureVisibility, $pressureVisibility, $altitudeVisibility, $dataRecording, $temperatureOffset, $pressureOffset, $altitudeOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $bmpSensorForm);
            }
        } elseif($sensor instanceof DHT) {

            //DHT Sensor
            $dhtSensorForm = new DHTSensorForm($sensor);
            $dhtSensorForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $dhtSensorForm->addId('shc-view-form-editSensor');

            if($dhtSensorForm->isSubmitted() && $dhtSensorForm->validate()) {

                //Speichern
                $name = $dhtSensorForm->getElementByName('name')->getValue();
                $icon = $dhtSensorForm->getElementByName('icon')->getValue();
                $rooms = $dhtSensorForm->getElementByName('rooms')->getValues();
                $visibility = $dhtSensorForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $dhtSensorForm->getElementByName('temperatureVisibility')->getValue();
                $humidityVisibility = $dhtSensorForm->getElementByName('humidityVisibility')->getValue();
                $temperatureOffset = $dhtSensorForm->getElementByName('tempOffset')->getValue();
                $humidityOffset = $dhtSensorForm->getElementByName('humOffset')->getValue();
                $dataRecording = $dhtSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editDHT($sensorId, $name, $icon, $rooms, null, $visibility, $temperatureVisibility, $humidityVisibility, $dataRecording, $temperatureOffset, $humidityOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $dhtSensorForm);
            }
        } elseif($sensor instanceof DS18x20) {

            //DHT Sensor
            $ds18x20SensorForm = new DS18x20SensorForm($sensor);
            $ds18x20SensorForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $ds18x20SensorForm->addId('shc-view-form-editSensor');

            if($ds18x20SensorForm->isSubmitted() && $ds18x20SensorForm->validate()) {

                //Speichern
                $name = $ds18x20SensorForm->getElementByName('name')->getValue();
                $icon = $ds18x20SensorForm->getElementByName('icon')->getValue();
                $rooms = $ds18x20SensorForm->getElementByName('rooms')->getValues();
                $visibility = $ds18x20SensorForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $ds18x20SensorForm->getElementByName('temperatureVisibility')->getValue();
                $temperatureOffset = $ds18x20SensorForm->getElementByName('tempOffset')->getValue();
                $dataRecording = $ds18x20SensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editDS18x20($sensorId, $name, $icon, $rooms, null, $visibility, $temperatureVisibility, $dataRecording, $temperatureOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $ds18x20SensorForm);
            }
        } elseif($sensor instanceof Hygrometer) {

            //Hygrometer Sensor
            $hygrometerSensorForm = new HygrometerSensorForm($sensor);
            $hygrometerSensorForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $hygrometerSensorForm->addId('shc-view-form-editSensor');

            if($hygrometerSensorForm->isSubmitted() && $hygrometerSensorForm->validate()) {

                //Speichern
                $name = $hygrometerSensorForm->getElementByName('name')->getValue();
                $icon = $hygrometerSensorForm->getElementByName('icon')->getValue();
                $rooms = $hygrometerSensorForm->getElementByName('rooms')->getValues();
                $visibility = $hygrometerSensorForm->getElementByName('visibility')->getValue();
                $valueVisibility = $hygrometerSensorForm->getElementByName('valueVisibility')->getValue();
                $valueOffset = $hygrometerSensorForm->getElementByName('valOffset')->getValue();
                $dataRecording = $hygrometerSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editHygrometer($sensorId, $name, $icon, $rooms, null, $visibility, $valueVisibility, $dataRecording, $valueOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $hygrometerSensorForm);
            }
        } elseif($sensor instanceof RainSensor) {

            //Regensensor
            $rainSensorForm = new RainSensorForm($sensor);
            $rainSensorForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $rainSensorForm->addId('shc-view-form-editSensor');

            if($rainSensorForm->isSubmitted() && $rainSensorForm->validate()) {

                //Speichern
                $name = $rainSensorForm->getElementByName('name')->getValue();
                $icon = $rainSensorForm->getElementByName('icon')->getValue();
                $rooms = $rainSensorForm->getElementByName('rooms')->getValues();
                $visibility = $rainSensorForm->getElementByName('visibility')->getValue();
                $valueVisibility = $rainSensorForm->getElementByName('valueVisibility')->getValue();
                $valueOffset = $rainSensorForm->getElementByName('valOffset')->getValue();
                $dataRecording = $rainSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editRainSensor($sensorId, $name, $icon, $rooms, null, $visibility, $valueVisibility, $dataRecording, $valueOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $rainSensorForm);
            }
        } elseif($sensor instanceof LDR) {

            //Lichtsensor
            $ldrSensorForm = new LDRSensorForm($sensor);
            $ldrSensorForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $ldrSensorForm->addId('shc-view-form-editSensor');

            if($ldrSensorForm->isSubmitted() && $ldrSensorForm->validate()) {

                //Speichern
                $name = $ldrSensorForm->getElementByName('name')->getValue();
                $icon = $ldrSensorForm->getElementByName('icon')->getValue();
                $rooms = $ldrSensorForm->getElementByName('rooms')->getValues();
                $visibility = $ldrSensorForm->getElementByName('visibility')->getValue();
                $valueVisibility = $ldrSensorForm->getElementByName('valueVisibility')->getValue();
                $valueOffset = $ldrSensorForm->getElementByName('valOffset')->getValue();
                $dataRecording = $ldrSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editLDR($sensorId, $name, $icon, $rooms, null, $visibility, $valueVisibility, $dataRecording, $valueOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $ldrSensorForm);
            }
        } elseif($sensor instanceof AvmMeasuringSocket) {

            //AVM Power Steckdose
            $avmMeasuringSocketForm = new AvmMeasuringSocketForm($sensor);
            $avmMeasuringSocketForm->setAction('index.php?app=shc&page=editsensorform&id='. $sensor->getId());
            $avmMeasuringSocketForm->addId('shc-view-form-editSensor');

            if($avmMeasuringSocketForm->isSubmitted() && $avmMeasuringSocketForm->validate()) {

                //Speichern
                $name = $avmMeasuringSocketForm->getElementByName('name')->getValue();
                $icon = $avmMeasuringSocketForm->getElementByName('icon')->getValue();
                $rooms = $avmMeasuringSocketForm->getElementByName('rooms')->getValues();
                $visibility = $avmMeasuringSocketForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $avmMeasuringSocketForm->getElementByName('temperatureVisibility')->getValue();
                $powerVisibility = $avmMeasuringSocketForm->getElementByName('powerVisibility')->getValue();
                $energyVisibility = $avmMeasuringSocketForm->getElementByName('energyVisibility')->getValue();
                $temperatureOffset = $avmMeasuringSocketForm->getElementByName('tempOffset')->getValue();
                $dataRecording = $avmMeasuringSocketForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editAvmMeasuringSensor($sensorId, $name, $icon, $rooms, null, $visibility, $temperatureVisibility, $powerVisibility, $energyVisibility, $dataRecording, $temperatureOffset);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.success'));
                } catch(\Exception $e) {

                    if($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editSensor.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $avmMeasuringSocketForm);
            }
        } else {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&page=listswitchables');
            $this->response->setBody('');
            $this->template = '';
        }
    }

}