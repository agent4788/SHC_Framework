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
use SHC\Form\FormElements\ElementTypeChooser;
use SHC\Form\Forms\Elements\ActivityForm;
use SHC\Form\Forms\Elements\AvmSocketForm;
use SHC\Form\Forms\Elements\CountdownForm;
use SHC\Form\Forms\Elements\EdimaxSocketForm;
use SHC\Form\Forms\Elements\FritzBoxForm;
use SHC\Form\Forms\Elements\RadiosocketForm;
use SHC\Form\Forms\Elements\RebootForm;
use SHC\Form\Forms\Elements\RpiGpioInputForm;
use SHC\Form\Forms\Elements\RpiGpioOutputForm;
use SHC\Form\Forms\Elements\ScriptForm;
use SHC\Form\Forms\Elements\ShutdownForm;
use SHC\Form\Forms\Elements\VirtualSocketForm;
use SHC\Form\Forms\Elements\WolForm;
use SHC\Sensor\SensorPointEditor;
use SHC\Switchable\SwitchableEditor;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddElementFormPage extends PageCommand {

    protected $template = 'addelementform.html';

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

        //Typ ermitteln
        if(RWF::getRequest()->issetParam('type', Request::GET) || RWF::getSession()->issetVar('type')) {

            //Neues Formular
            if (RWF::getSession()->issetVar('type')) {

                $type = RWF::getSession()->get('type');
            } else {

                $type = RWF::getRequest()->getParam('type', Request::GET, DataTypeUtil::INTEGER);
            }

            RWF::getSession()->set('type', $type);
            switch ($type) {

                case SwitchableEditor::TYPE_ACTIVITY:

                    //Aktivitaet
                    $activityForm = new ActivityForm();
                    $activityForm->setAction('index.php?app=shc&m&page=addelementform');
                    $activityForm->setView(Form::SMARTPHONE_VIEW);
                    $activityForm->addId('shc-view-form-addElement');

                    if($activityForm->isSubmitted() && $activityForm->validate()) {

                        //Speichern
                        $name = $activityForm->getElementByName('name')->getValue();
                        $icon = $activityForm->getElementByName('icon')->getValue();
                        $buttonText = $activityForm->getElementByName('buttonText')->getValue();
                        $rooms = $activityForm->getElementByName('rooms')->getValues();
                        $enabled = $activityForm->getElementByName('enabled')->getValue();
                        $visibility = $activityForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $activityForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addActivity($name, $enabled, $visibility, $icon, $rooms, array(), array(), $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $activityForm);
                    }
                    break;
                case SwitchableEditor::TYPE_COUNTDOWN:

                    //Countdown
                    $countdownForm = new CountdownForm();
                    $countdownForm->setAction('index.php?app=shc&m&page=addelementform');
                    $countdownForm->setView(Form::SMARTPHONE_VIEW);
                    $countdownForm->addId('shc-view-form-addElement');

                    if($countdownForm->isSubmitted() && $countdownForm->validate()) {

                        //Speichern
                        $name = $countdownForm->getElementByName('name')->getValue();
                        $icon = $countdownForm->getElementByName('icon')->getValue();
                        $buttonText = $countdownForm->getElementByName('buttonText')->getValue();
                        $rooms = $countdownForm->getElementByName('rooms')->getValues();
                        $interval = $countdownForm->getElementByName('interval')->getValue();
                        $enabled = $countdownForm->getElementByName('enabled')->getValue();
                        $visibility = $countdownForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $countdownForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addCountdown($name, $enabled, $visibility, $icon, $rooms, array(), $interval, array(), $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $countdownForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RADIOSOCKET:

                    //Funksteckdose
                    $radiosocketForm = new RadiosocketForm();
                    $radiosocketForm->setAction('index.php?app=shc&m&page=addelementform');
                    $radiosocketForm->setView(Form::SMARTPHONE_VIEW);
                    $radiosocketForm->addId('shc-view-form-addElement');

                    if($radiosocketForm->isSubmitted() && $radiosocketForm->validate()) {

                        //Speichern
                        $name = $radiosocketForm->getElementByName('name')->getValue();
                        $icon = $radiosocketForm->getElementByName('icon')->getValue();
                        $buttonText = $radiosocketForm->getElementByName('buttonText')->getValue();
                        $rooms = $radiosocketForm->getElementByName('rooms')->getValues();
                        $protocol = $radiosocketForm->getElementByName('protocol')->getValue();
                        $systemCode = $radiosocketForm->getElementByName('systemCode')->getValue();
                        $deviceCode = $radiosocketForm->getElementByName('deviceCode')->getValue();
                        $continuous = $radiosocketForm->getElementByName('continuous')->getValue();
                        $enabled = $radiosocketForm->getElementByName('enabled')->getValue();
                        $visibility = $radiosocketForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $radiosocketForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addRadioSocket($name, $enabled, $visibility, $icon, $rooms, array(), $protocol, $systemCode, $deviceCode, $continuous, array(), $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $radiosocketForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RPI_GPIO_OUTPUT:

                    //GPIO Ausgang
                    $gpioOutputForm = new RpiGpioOutputForm();
                    $gpioOutputForm->setAction('index.php?app=shc&m&page=addelementform');
                    $gpioOutputForm->setView(Form::SMARTPHONE_VIEW);
                    $gpioOutputForm->addId('shc-view-form-addElement');

                    if($gpioOutputForm->isSubmitted() && $gpioOutputForm->validate()) {

                        //Speichern
                        $name = $gpioOutputForm->getElementByName('name')->getValue();
                        $icon = $gpioOutputForm->getElementByName('icon')->getValue();
                        $buttonText = $gpioOutputForm->getElementByName('buttonText')->getValue();
                        $rooms = $gpioOutputForm->getElementByName('rooms')->getValues();
                        $switchServer = $gpioOutputForm->getElementByName('switchServer')->getValue();
                        $gpioPin = $gpioOutputForm->getElementByName('gpio')->getValue();
                        $enabled = $gpioOutputForm->getElementByName('enabled')->getValue();
                        $visibility = $gpioOutputForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $gpioOutputForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addRriGpioOutput($name, $enabled, $visibility, $icon, $rooms, array(), $switchServer, $gpioPin, array(), $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $gpioOutputForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RPI_GPIO_INPUT:

                    //GPIO Eingang
                    $gpioInputForm = new RpiGpioInputForm();
                    $gpioInputForm->setAction('index.php?app=shc&m&page=addelementform');
                    $gpioInputForm->setView(Form::SMARTPHONE_VIEW);
                    $gpioInputForm->addId('shc-view-form-addElement');

                    if($gpioInputForm->isSubmitted() && $gpioInputForm->validate()) {

                        //Speichern
                        $name = $gpioInputForm->getElementByName('name')->getValue();
                        $icon = '';
                        $rooms = $gpioInputForm->getElementByName('rooms')->getValues();
                        $switchServer = $gpioInputForm->getElementByName('switchServer')->getValue();
                        $gpioPin = $gpioInputForm->getElementByName('gpio')->getValue();
                        $enabled = $gpioInputForm->getElementByName('enabled')->getValue();
                        $visibility = $gpioInputForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $gpioInputForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addRriGpioInput($name, $enabled, $visibility, $icon, $rooms, array(), $switchServer, $gpioPin, $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $gpioInputForm);
                    }
                    break;
                case SwitchableEditor::TYPE_WAKEONLAN:

                    //Wake On Lan
                    $wolForm = new WolForm();
                    $wolForm->setAction('index.php?app=shc&m&page=addelementform');
                    $wolForm->setView(Form::SMARTPHONE_VIEW);
                    $wolForm->addId('shc-view-form-addElement');

                    if($wolForm->isSubmitted() && $wolForm->validate()) {

                        //Speichern
                        $name = $wolForm->getElementByName('name')->getValue();
                        $icon = '';
                        $rooms = $wolForm->getElementByName('rooms')->getValues();
                        $mac = $wolForm->getElementByName('mac')->getValue();
                        $ip = $wolForm->getElementByName('ip')->getValue();
                        $enabled = $wolForm->getElementByName('enabled')->getValue();
                        $visibility = $wolForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $wolForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addWakeOnLan($name, $enabled, $visibility, $icon, $rooms, array(), $mac, $ip, array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $wolForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RADIOSOCKET_DIMMER:

                    //Nicht Implementiert
                    break;
                case SwitchableEditor::TYPE_REBOOT:

                    $rebootForm = new RebootForm();
                    $rebootForm->setAction('index.php?app=shc&m&page=addelementform');
                    $rebootForm->setView(Form::SMARTPHONE_VIEW);
                    $rebootForm->addId('shc-view-form-addElement');

                    if($rebootForm->isSubmitted() && $rebootForm->validate()) {

                        //Speichern
                        $name = $rebootForm->getElementByName('name')->getValue();
                        $icon = '';
                        $rooms = $rebootForm->getElementByName('rooms')->getValues();
                        $enabled = $rebootForm->getElementByName('enabled')->getValue();
                        $visibility = $rebootForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $rebootForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addReboot($name, $enabled, $visibility, $icon, $rooms, array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addReboot.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addReboot.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addReboot.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $rebootForm);
                    }
                    break;
                case SwitchableEditor::TYPE_SHUTDOWN:

                    $shutdownForm = new ShutdownForm();
                    $shutdownForm->setAction('index.php?app=shc&m&page=addelementform');
                    $shutdownForm->setView(Form::SMARTPHONE_VIEW);
                    $shutdownForm->addId('shc-view-form-addElement');

                    if($shutdownForm->isSubmitted() && $shutdownForm->validate()) {

                        //Speichern
                        $name = $shutdownForm->getElementByName('name')->getValue();
                        $icon = '';
                        $rooms = $shutdownForm->getElementByName('rooms')->getValues();
                        $enabled = $shutdownForm->getElementByName('enabled')->getValue();
                        $visibility = $shutdownForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $shutdownForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addShutdown($name, $enabled, $visibility, $icon, $rooms, array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $shutdownForm);
                    }
                    break;
                case SwitchableEditor::TYPE_REMOTE_REBOOT:

                    //Nicht Implementiert
                    break;
                case SwitchableEditor::TYPE_REMOTE_SHUTDOWN:

                    //Nicht Implementiert
                    break;
                case SwitchableEditor::TYPE_SCRIPT:

                    $scriptForm = new ScriptForm();
                    $scriptForm->setAction('index.php?app=shc&m&page=addelementform');
                    $scriptForm->setView(Form::SMARTPHONE_VIEW);
                    $scriptForm->addId('shc-view-form-addElement');

                    if($scriptForm->isSubmitted() && $scriptForm->validate()) {

                        //Speichern
                        $name = $scriptForm->getElementByName('name')->getValue();
                        $icon = $scriptForm->getElementByName('icon')->getValue();
                        $buttonText = $scriptForm->getElementByName('buttonText')->getValue();
                        $rooms = $scriptForm->getElementByName('rooms')->getValues();
                        $onCommand = $scriptForm->getElementByName('onCommand')->getValue();
                        $offCommand = $scriptForm->getElementByName('offCommand')->getValue();
                        $enabled = $scriptForm->getElementByName('enabled')->getValue();
                        $visibility = $scriptForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $scriptForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addScript($name, $enabled, $visibility, $icon, $rooms, array(), $onCommand, $offCommand, $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $scriptForm);
                    }
                    break;
                case SwitchableEditor::TYPE_AVM_SOCKET:

                    $avmSocketForm = new AvmSocketForm();
                    $avmSocketForm->setAction('index.php?app=shc&m&page=addelementform');
                    $avmSocketForm->setView(Form::SMARTPHONE_VIEW);
                    $avmSocketForm->addId('shc-view-form-addElement');

                    if($avmSocketForm->isSubmitted() && $avmSocketForm->validate()) {

                        //Speichern
                        $name = $avmSocketForm->getElementByName('name')->getValue();
                        $icon = $avmSocketForm->getElementByName('icon')->getValue();
                        $buttonText = $avmSocketForm->getElementByName('buttonText')->getValue();
                        $rooms = $avmSocketForm->getElementByName('rooms')->getValues();
                        $ain = $avmSocketForm->getElementByName('ain')->getValue();
                        $enabled = $avmSocketForm->getElementByName('enabled')->getValue();
                        $visibility = $avmSocketForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $avmSocketForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addAvmSocket($name, $enabled, $visibility, $icon, $rooms, array(), $ain, $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addAvmSocket.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $avmSocketForm);
                    }
                    break;
                case SwitchableEditor::TYPE_FRITZBOX:

                    $fritzBox= new FritzBoxForm();
                    $fritzBox->setAction('index.php?app=shc&m&page=addelementform');
                    $fritzBox->setView(Form::SMARTPHONE_VIEW);
                    $fritzBox->addId('shc-view-form-addElement');

                    if($fritzBox->isSubmitted() && $fritzBox->validate()) {

                        //Speichern
                        $rooms = $fritzBox->getElementByName('rooms')->getValues();
                        $function = $fritzBox->getElementByName('function')->getValue();
                        $enabled = $fritzBox->getElementByName('enabled')->getValue();
                        $visibility = $fritzBox->getElementByName('visibility')->getValue();
                        $allowedUsers = $fritzBox->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addFritzBox('', $enabled, $visibility, '', $rooms, array(), $function, $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addFritzBox.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $fritzBox);
                    }
                    break;
                case SwitchableEditor::TYPE_EDIMAX_SOCKET:

                    $edimaxSocket = new EdimaxSocketForm();
                    $edimaxSocket->setAction('index.php?app=shc&m&page=addelementform');
                    $edimaxSocket->setView(Form::SMARTPHONE_VIEW);
                    $edimaxSocket->addId('shc-view-form-addElement');

                    if($edimaxSocket->isSubmitted() && $edimaxSocket->validate()) {

                        //Speichern
                        $name = $edimaxSocket->getElementByName('name')->getValue();
                        $icon = $edimaxSocket->getElementByName('icon')->getValue();
                        $buttonText = $edimaxSocket->getElementByName('buttonText')->getValue();
                        $rooms = $edimaxSocket->getElementByName('rooms')->getValues();
                        $ip = $edimaxSocket->getElementByName('ip')->getValue();
                        $type = $edimaxSocket->getElementByName('type')->getValue();
                        $username = $edimaxSocket->getElementByName('username')->getValue();
                        $password = $edimaxSocket->getElementByName('password')->getValue();
                        $enabled = $edimaxSocket->getElementByName('enabled')->getValue();
                        $visibility = $edimaxSocket->getElementByName('visibility')->getValue();
                        $allowedUsers = $edimaxSocket->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addEdimaxSocket($name, $enabled, $visibility, $icon, $rooms, array(), $ip, $type, $username, $password, $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.eidmaxSocket.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.eidmaxSocket.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.eidmaxSocket.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $edimaxSocket);
                    }
                    break;
                case SwitchableEditor::TYPE_VIRTUAL_SOCKET:

                    $virtualSocket = new VirtualSocketForm();
                    $virtualSocket->setAction('index.php?app=shc&m&page=addelementform');
                    $virtualSocket->setView(Form::SMARTPHONE_VIEW);
                    $virtualSocket->addId('shc-view-form-addElement');

                    if($virtualSocket->isSubmitted() && $virtualSocket->validate()) {

                        //Speichern
                        $name = $virtualSocket->getElementByName('name')->getValue();
                        $icon = $virtualSocket->getElementByName('icon')->getValue();
                        $buttonText = $virtualSocket->getElementByName('buttonText')->getValue();
                        $rooms = $virtualSocket->getElementByName('rooms')->getValues();
                        $enabled = $virtualSocket->getElementByName('enabled')->getValue();
                        $visibility = $virtualSocket->getElementByName('visibility')->getValue();
                        $allowedUsers = $virtualSocket->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addVirtualSocket($name, $enabled, $visibility, $icon, $rooms, array(), $allowedUsers, $buttonText);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.virtualSocket.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchables');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('elementForm', $virtualSocket);
                    }
                    break;
                case SensorPointEditor::VSENSOR_ENERGY:
                case SensorPointEditor::VSENSOR_FLUID_AMOUNT:
                case SensorPointEditor::VSENSOR_HUMIDITY:
                case SensorPointEditor::VSENSOR_LIGHT_INTENSITY:
                case SensorPointEditor::VSENSOR_MOISTURE:
                case SensorPointEditor::VSENSOR_POWER:
                case SensorPointEditor::VSENSOR_TEMPERATURE:

                    //virtuellen Sensor erstellen und auf Edit Formular umleiten
                    $message = new Message();
                    try {

                        $newSensorId = SensorPointEditor::getInstance()->createVirtualSensor($type);
                        SHC::getResponse()->addLocationHeader('index.php?app=shc&page=editsensorform&id=' . $newSensorId);
                        SHC::getResponse()->setBody('');
                        SHC::getResponse()->flush();
                        exit();
                    } catch(\Exception $e) {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addVSensor.error'));
                    }
                    break;
            }
        } else {

            //Typauswahl Anzeigen
            $elementTypeChooser = new ElementTypeChooser('type');
            $tpl->assign('elementTypeChooser', $elementTypeChooser);
        }
    }

}