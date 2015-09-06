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
use SHC\Form\Forms\Elements\ActivityForm;
use SHC\Form\Forms\Elements\AvmSocketForm;
use SHC\Form\Forms\Elements\CountdownForm;
use SHC\Form\Forms\Elements\FritzBoxForm;
use SHC\Form\Forms\Elements\RadiosocketForm;
use SHC\Form\Forms\Elements\RebootForm;
use SHC\Form\Forms\Elements\RpiGpioInputForm;
use SHC\Form\Forms\Elements\RpiGpioOutputForm;
use SHC\Form\Forms\Elements\ScriptForm;
use SHC\Form\Forms\Elements\ShutdownForm;
use SHC\Form\Forms\Elements\WolForm;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RadioSocketDimmer;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RemoteReboot;
use SHC\Switchable\Switchables\RemoteShutdown;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditElementFormPage extends PageCommand {

    protected $template = 'editelementform.html';

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

        //Element Objekt laden
        $elementId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $element = SwitchableEditor::getInstance()->getElementById($elementId);

        //Formulare je nach Objekttyp erstellen
        if($element instanceof Activity) {

            //Aktivitaet
            $activityForm = new ActivityForm($element);
            $activityForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $activityForm->setView(Form::SMARTPHONE_VIEW);
            $activityForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editActivity($elementId, $name, $enabled, $visibility, $icon, $rooms, null, null, $allowedUsers, $buttonText);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editActivity.success'));
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
        } elseif($element instanceof Countdown) {

            //Countdown
            $countdownForm = new CountdownForm($element);
            $countdownForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $countdownForm->setView(Form::SMARTPHONE_VIEW);
            $countdownForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editCountdown($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $interval, null, $allowedUsers, $buttonText);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editCountdown.success'));
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
        } elseif($element instanceof RadioSocket) {

            //Funksteckdose
            $radiosocketForm = new RadiosocketForm($element);
            $radiosocketForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $radiosocketForm->setView(Form::SMARTPHONE_VIEW);
            $radiosocketForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editRadioSocket($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $protocol, $systemCode, $deviceCode, $continuous, null, $allowedUsers, $buttonText);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editRadioSocket.success'));
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
        } elseif($element instanceof RpiGpioInput) {

            //GPIO Eingang
            $gpioInputForm = new RpiGpioInputForm($element);
            $gpioInputForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $gpioInputForm->setView(Form::SMARTPHONE_VIEW);
            $gpioInputForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editRpiGpioInput($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $switchServer, $gpioPin, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editGpioOutput.success'));
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
        } elseif($element instanceof RpiGpioOutput) {

            //GPIO Ausgang
            $gpioOutputForm = new RpiGpioOutputForm($element);
            $gpioOutputForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $gpioOutputForm->setView(Form::SMARTPHONE_VIEW);
            $gpioOutputForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editRpiGpioOutput($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $switchServer, $gpioPin, null, $allowedUsers, $buttonText);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editGpioOutput.success'));
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
        } elseif($element instanceof WakeOnLan) {

            //Wake On Lan
            $wolForm = new WolForm($element);
            $wolForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $wolForm->setView(Form::SMARTPHONE_VIEW);
            $wolForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editWakeOnLan($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $mac, $ip, null, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editWol.success'));
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
        } elseif($element instanceof RadioSocketDimmer) {

            //nicht Implementiert
        } elseif($element instanceof Shutdown) {

            $shutdownForm = new ShutdownForm($element);
            $shutdownForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $shutdownForm->setView(Form::SMARTPHONE_VIEW);
            $shutdownForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editShutdown($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editShutdown.success'));
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
        } elseif($element instanceof Reboot) {

            $rebootForm = new RebootForm($element);
            $rebootForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $rebootForm->setView(Form::SMARTPHONE_VIEW);
            $rebootForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editReboot($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editReboot.success'));
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
        } elseif($element instanceof RemoteShutdown) {

            //nicht Implementiert
        } elseif($element instanceof RemoteReboot) {

            //nicht Implementiert
        } elseif($element instanceof Script) {

            $scriptForm = new ScriptForm($element);
            $scriptForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
            $scriptForm->setView(Form::SMARTPHONE_VIEW);
            $scriptForm->addId('shc-view-form-editElement');

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

                    SwitchableEditor::getInstance()->editScript($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $onCommand, $offCommand, $allowedUsers, $buttonText);
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
        } elseif($element instanceof AvmSocket) {

            $avmSocketForm = new AvmSocketForm($element);
            $avmSocketForm->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
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

                    SwitchableEditor::getInstance()->editAvmSocket($elementId, $name, $enabled, $visibility, $icon, $rooms, null, $ain, $allowedUsers, $buttonText);
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
        } elseif($element instanceof FritzBox) {

            $fritzBox= new FritzBoxForm($element);
            $fritzBox->setAction('index.php?app=shc&m&page=editelementform&id='. $element->getId());
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

                    SwitchableEditor::getInstance()->editFritzBox($elementId, $enabled, $visibility, '', $rooms, null, $function, $allowedUsers);
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
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            return;
        }
    }

}