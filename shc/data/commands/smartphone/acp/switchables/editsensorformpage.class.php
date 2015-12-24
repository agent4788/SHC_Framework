<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Form\Form;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\Sensors\AvmMeasuringSocketForm;
use SHC\Form\Forms\Sensors\BMPSensorForm;
use SHC\Form\Forms\Sensors\CometThermostatForm;
use SHC\Form\Forms\Sensors\DHTSensorForm;
use SHC\Form\Forms\Sensors\DS18x20SensorForm;
use SHC\Form\Forms\Sensors\EdimaxMeasuringSocketForm;
use SHC\Form\Forms\Sensors\HcSr04Form;
use SHC\Form\Forms\Sensors\HygrometerSensorForm;
use SHC\Form\Forms\Sensors\LDRSensorForm;
use SHC\Form\Forms\Sensors\RainSensorForm;
use SHC\Form\Forms\Sensors\SCT013Form;
use SHC\Form\Forms\Sensors\vSensorForm;
use SHC\Sensor\SensorPointEditor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\CometDectRadiatorThermostat;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\FluidAmount;
use SHC\Sensor\Sensors\EdimaxMeasuringSocket;
use SHC\Sensor\Sensors\GasMeter;
use SHC\Sensor\Sensors\HcSr04;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Sensor\Sensors\SCT013;
use SHC\Sensor\Sensors\WaterMeter;
use SHC\Sensor\vSensor;

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

    protected $template = 'editsensorform.html';

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

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listswitchables');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Sensor Objekt laden
        $sensorId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::STRING);
        $sensor = SensorPointEditor::getInstance()->getSensorById($sensorId);

        //Formulare je nach Objekttyp erstellen
        if($sensor instanceof BMP) {

            //BMP Sensor
            $bmpSensorForm = new BMPSensorForm($sensor);
            $bmpSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $bmpSensorForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $bmpSensorForm);
            }
        } elseif($sensor instanceof DHT) {

            //DHT Sensor
            $dhtSensorForm = new DHTSensorForm($sensor);
            $dhtSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $dhtSensorForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $dhtSensorForm);
            }
        } elseif($sensor instanceof DS18x20) {

            //DHT Sensor
            $ds18x20SensorForm = new DS18x20SensorForm($sensor);
            $ds18x20SensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $ds18x20SensorForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $ds18x20SensorForm);
            }
        } elseif($sensor instanceof Hygrometer) {

            //Hygrometer Sensor
            $hygrometerSensorForm = new HygrometerSensorForm($sensor);
            $hygrometerSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $hygrometerSensorForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $hygrometerSensorForm);
            }
        } elseif($sensor instanceof RainSensor) {

            //Regensensor
            $rainSensorForm = new RainSensorForm($sensor);
            $rainSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $rainSensorForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $rainSensorForm);
            }
        } elseif($sensor instanceof LDR) {

            //Lichtsensor
            $ldrSensorForm = new LDRSensorForm($sensor);
            $ldrSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $ldrSensorForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $ldrSensorForm);
            }
        } elseif($sensor instanceof AvmMeasuringSocket) {

            //AVM Power Steckdose
            $avmMeasuringSocketForm = new AvmMeasuringSocketForm($sensor);
            $avmMeasuringSocketForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $avmMeasuringSocketForm->setView(Form::SMARTPHONE_VIEW);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $avmMeasuringSocketForm);
            }
        } elseif($sensor instanceof EdimaxMeasuringSocket) {

            //Edimax Power Steckdose
            $edimaxMeasuringSocketForm = new EdimaxMeasuringSocketForm($sensor);
            $edimaxMeasuringSocketForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $edimaxMeasuringSocketForm->setView(Form::SMARTPHONE_VIEW);
            $edimaxMeasuringSocketForm->addId('shc-view-form-editSensor');

            if($edimaxMeasuringSocketForm->isSubmitted() && $edimaxMeasuringSocketForm->validate()) {

                //Speichern
                $name = $edimaxMeasuringSocketForm->getElementByName('name')->getValue();
                $icon = $edimaxMeasuringSocketForm->getElementByName('icon')->getValue();
                $rooms = $edimaxMeasuringSocketForm->getElementByName('rooms')->getValues();
                $visibility = $edimaxMeasuringSocketForm->getElementByName('visibility')->getValue();
                $powerVisibility = $edimaxMeasuringSocketForm->getElementByName('powerVisibility')->getValue();
                $energyVisibility = $edimaxMeasuringSocketForm->getElementByName('energyVisibility')->getValue();
                $dataRecording = $edimaxMeasuringSocketForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editAvmMeasuringSensor($sensorId, $name, $icon, $rooms, null, $visibility, $powerVisibility, $energyVisibility, $dataRecording);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $edimaxMeasuringSocketForm);
            }
        } elseif($sensor instanceof GasMeter) {

            //Gaszähler
            $gasMeterSensorForm = new LDRSensorForm($sensor);
            $gasMeterSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $gasMeterSensorForm->setView(Form::SMARTPHONE_VIEW);
            $gasMeterSensorForm->addId('shc-view-form-editSensor');

            if($gasMeterSensorForm->isSubmitted() && $gasMeterSensorForm->validate()) {

                //Speichern
                $name = $gasMeterSensorForm->getElementByName('name')->getValue();
                $icon = $gasMeterSensorForm->getElementByName('icon')->getValue();
                $rooms = $gasMeterSensorForm->getElementByName('rooms')->getValues();
                $visibility = $gasMeterSensorForm->getElementByName('visibility')->getValue();
                $fluidAmountVisibility = $gasMeterSensorForm->getElementByName('fluidAmountVisibility')->getValue();
                $dataRecording = $gasMeterSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editGasmeter($sensorId, $name, $icon, $rooms, null, $visibility, $fluidAmountVisibility, $dataRecording, $valueOffset);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $gasMeterSensorForm);
            }
        } elseif($sensor instanceof WaterMeter) {

            //Wasserzähler
            $waterMeterSensorForm = new LDRSensorForm($sensor);
            $waterMeterSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $waterMeterSensorForm->setView(Form::SMARTPHONE_VIEW);
            $waterMeterSensorForm->addId('shc-view-form-editSensor');

            if($waterMeterSensorForm->isSubmitted() && $waterMeterSensorForm->validate()) {

                //Speichern
                $name = $waterMeterSensorForm->getElementByName('name')->getValue();
                $icon = $waterMeterSensorForm->getElementByName('icon')->getValue();
                $rooms = $waterMeterSensorForm->getElementByName('rooms')->getValues();
                $visibility = $waterMeterSensorForm->getElementByName('visibility')->getValue();
                $fluidAmountVisibility = $waterMeterSensorForm->getElementByName('fluidAmountVisibility')->getValue();
                $dataRecording = $waterMeterSensorForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editWatermeter($sensorId, $name, $icon, $rooms, null, $visibility, $fluidAmountVisibility, $dataRecording);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $waterMeterSensorForm);
            }
        } elseif($sensor instanceof CometDectRadiatorThermostat) {

            //Comet Thermostat
            $cometThermostatForm = new CometThermostatForm($sensor);
            $cometThermostatForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $cometThermostatForm->setView(Form::SMARTPHONE_VIEW);
            $cometThermostatForm->addId('shc-view-form-editSensor');

            if($cometThermostatForm->isSubmitted() && $cometThermostatForm->validate()) {

                //Speichern
                $name = $cometThermostatForm->getElementByName('name')->getValue();
                $icon = $cometThermostatForm->getElementByName('icon')->getValue();
                $rooms = $cometThermostatForm->getElementByName('rooms')->getValues();
                $visibility = $cometThermostatForm->getElementByName('visibility')->getValue();
                $temperatureVisibility = $cometThermostatForm->getElementByName('temperatureVisibility')->getValue();
                $temperatureOffset = $cometThermostatForm->getElementByName('tempOffset')->getValue();
                $dataRecording = $cometThermostatForm->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editCometDectRadiatoThermostatSensor($sensorId, $name, $icon, $rooms, null, $visibility, $temperatureVisibility, $dataRecording, $temperatureOffset);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $cometThermostatForm);
            }
        } elseif($sensor instanceof HcSr04) {

            //HC-SR04 Entfernungsmesser
            $hcSr04Form = new HcSr04Form($sensor);
            $hcSr04Form->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $hcSr04Form->setView(Form::SMARTPHONE_VIEW);
            $hcSr04Form->addId('shc-view-form-editSensor');

            if($hcSr04Form->isSubmitted() && $hcSr04Form->validate()) {

                //Speichern
                $name = $hcSr04Form->getElementByName('name')->getValue();
                $icon = $hcSr04Form->getElementByName('icon')->getValue();
                $rooms = $hcSr04Form->getElementByName('rooms')->getValues();
                $visibility = $hcSr04Form->getElementByName('visibility')->getValue();
                $distanceVisibility = $hcSr04Form->getElementByName('distanceVisibility')->getValue();
                $distanceOffset = $hcSr04Form->getElementByName('distanceOffset')->getValue();
                $dataRecording = $hcSr04Form->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editHcSr04($sensorId, $name, $icon, $rooms, null, $visibility, $distanceVisibility, $dataRecording, $distanceOffset);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $hcSr04Form);
            }
        } elseif($sensor instanceof SCT013) {

            //SCT-013 Energiemesser
            $sct013Form = new SCT013Form($sensor);
            $sct013Form->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $sct013Form->setView(Form::SMARTPHONE_VIEW);
            $sct013Form->addId('shc-view-form-editSensor');

            if($sct013Form->isSubmitted() && $sct013Form->validate()) {

                //Speichern
                $name = $sct013Form->getElementByName('name')->getValue();
                $icon = $sct013Form->getElementByName('icon')->getValue();
                $rooms = $sct013Form->getElementByName('rooms')->getValues();
                $visibility = $sct013Form->getElementByName('visibility')->getValue();
                $powerVisibility = $sct013Form->getElementByName('powerVisibility')->getValue();
                $dataRecording = $sct013Form->getElementByName('dataRecording')->getValue();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editSct013($sensorId, $name, $icon, $rooms, null, $visibility, $powerVisibility, $dataRecording);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $sct013Form);
            }
        } elseif($sensor instanceof vSensor) {

            //virtueller Sensor
            $vSensorForm = new vSensorForm($sensor);
            $vSensorForm->setAction('index.php?app=shc&m&page=editsensorform&id='. $sensor->getId());
            $vSensorForm->setView(Form::SMARTPHONE_VIEW);
            $vSensorForm->addId('shc-view-form-editSensor');

            if($vSensorForm->isSubmitted() && $vSensorForm->validate()) {

                //Speichern
                $name = $vSensorForm->getElementByName('name')->getValue();
                $icon = $vSensorForm->getElementByName('icon')->getValue();
                $rooms = $vSensorForm->getElementByName('rooms')->getValues();
                $visibility = $vSensorForm->getElementByName('visibility')->getValue();
                $sensors = $vSensorForm->getElementByName('sensors')->getValues();

                $message = new Message();
                try {

                    SensorPointEditor::getInstance()->editVirtualSensor($sensorId, $name, $icon, $rooms, null, $visibility, $sensors);
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
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('sensorForm', $vSensorForm);
            }
        } else {

            //Ungueltige ID
            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
            $this->response->setBody('');
            $this->template = '';
        }
    }

}