<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Form\FormElements\ElementTypeChooser;
use SHC\Form\Forms\ActivityForm;
use SHC\Form\Forms\CountdownForm;
use SHC\Form\Forms\RadiosocketForm;
use SHC\Form\Forms\RebootForm;
use SHC\Form\Forms\RpiGpioInputForm;
use SHC\Form\Forms\RpiGpioOutputForm;
use SHC\Form\Forms\ScriptForm;
use SHC\Form\Forms\ShutdownForm;
use SHC\Form\Forms\WolForm;
use SHC\Switchable\SwitchableEditor;
use SHC\View\Room\ViewHelperEditor;

/**
 * erstellt ein neues Element
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddElementFormAjax extends AjaxCommand {

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

        //Typ ermitteln
        if(RWF::getRequest()->issetParam('type', Request::GET) || RWF::getSession()->issetVar('type')) {

            //Neues Formular
            if(RWF::getSession()->issetVar('type')) {

                $type = RWF::getSession()->get('type');
            } else {

                $type = RWF::getRequest()->getParam('type', Request::GET, DataTypeUtil::INTEGER);
            }

            RWF::getSession()->set('type', $type);
            switch($type) {

                case SwitchableEditor::TYPE_ACTIVITY:

                    //Aktivitaet
                    $activityForm = new ActivityForm();
                    $activityForm->addId('shc-view-form-addElement');

                    if($activityForm->isSubmitted() && $activityForm->validate()) {

                        //Speichern
                        $name = $activityForm->getElementByName('name')->getValue();
                        $icon = $activityForm->getElementByName('icon')->getValue();;
                        $rooms = $activityForm->getElementByName('rooms')->getValues();
                        $enabled = $activityForm->getElementByName('enabled')->getValue();
                        $visibility = $activityForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $activityForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addActivity($name, $enabled, $visibility, $icon, $rooms, array(), array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addActivity.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $activityForm);
                    }
                    break;
                case SwitchableEditor::TYPE_COUNTDOWN:

                    //Countdown
                    $countdownForm = new CountdownForm();
                    $countdownForm->addId('shc-view-form-addElement');

                    if($countdownForm->isSubmitted() && $countdownForm->validate()) {

                        //Speichern
                        $name = $countdownForm->getElementByName('name')->getValue();
                        $icon = $countdownForm->getElementByName('icon')->getValue();;
                        $rooms = $countdownForm->getElementByName('rooms')->getValues();
                        $interval = $countdownForm->getElementByName('interval')->getValue();
                        $enabled = $countdownForm->getElementByName('enabled')->getValue();
                        $visibility = $countdownForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $countdownForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addCountdown($name, $enabled, $visibility, $icon, $rooms, array(), $interval, array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addCountdown.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $countdownForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RADIOSOCKET:

                    //Funksteckdose
                    $radiosocketForm = new RadiosocketForm();
                    $radiosocketForm->addId('shc-view-form-addElement');

                    if($radiosocketForm->isSubmitted() && $radiosocketForm->validate()) {

                        //Speichern
                        $name = $radiosocketForm->getElementByName('name')->getValue();
                        $icon = $radiosocketForm->getElementByName('icon')->getValue();
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

                            SwitchableEditor::getInstance()->addRadioSocket($name, $enabled, $visibility, $icon, $rooms, array(), $protocol, $systemCode, $deviceCode, $continuous, array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addRadioSocket.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $radiosocketForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RPI_GPIO_OUTPUT:

                    //GPIO Ausgang
                    $gpioOutputForm = new RpiGpioOutputForm();
                    $gpioOutputForm->addId('shc-view-form-addElement');

                    if($gpioOutputForm->isSubmitted() && $gpioOutputForm->validate()) {

                        //Speichern
                        $name = $gpioOutputForm->getElementByName('name')->getValue();
                        $icon = $gpioOutputForm->getElementByName('icon')->getValue();
                        $rooms = $gpioOutputForm->getElementByName('rooms')->getValues();
                        $switchServer = $gpioOutputForm->getElementByName('switchServer')->getValue();
                        $gpioPin = $gpioOutputForm->getElementByName('gpio')->getValue();
                        $enabled = $gpioOutputForm->getElementByName('enabled')->getValue();
                        $visibility = $gpioOutputForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $gpioOutputForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addRriGpioOutput($name, $enabled, $visibility, $icon, $rooms, array(), $switchServer, $gpioPin, array(), $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $gpioOutputForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RPI_GPIO_INPUT:

                    //GPIO Eingang
                    $gpioInputForm = new RpiGpioInputForm();
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

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addGpioOutput.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $gpioInputForm);
                    }
                    break;
                case SwitchableEditor::TYPE_WAKEONLAN:

                    //Wake On Lan
                    $wolForm = new WolForm();
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

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addWol.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $wolForm);
                    }
                    break;
                case SwitchableEditor::TYPE_RADIOSOCKET_DIMMER:

                    //Nicht Implementiert
                    break;
                case SwitchableEditor::TYPE_REBOOT:

                    $rebootForm = new RebootForm();
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

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addReboot.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addReboot.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addReboot.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $rebootForm);
                    }
                    break;
                case SwitchableEditor::TYPE_SHUTDOWN:

                    $shutdownForm = new ShutdownForm();
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

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addShutdown.error'));
                            }
                        }
                        $tpl->assign('message', $message);
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
                    $scriptForm->addId('shc-view-form-addElement');

                    if($scriptForm->isSubmitted() && $scriptForm->validate()) {

                        //Speichern
                        $name = $scriptForm->getElementByName('name')->getValue();
                        $icon = $scriptForm->getElementByName('icon')->getValue();
                        $rooms = $scriptForm->getElementByName('rooms')->getValues();
                        $onCommand = $scriptForm->getElementByName('onCommand')->getValue();
                        $offCommand = $scriptForm->getElementByName('offCommand')->getValue();
                        $enabled = $scriptForm->getElementByName('enabled')->getValue();
                        $visibility = $scriptForm->getElementByName('visibility')->getValue();
                        $allowedUsers = $scriptForm->getElementByName('allowedUsers')->getValues();

                        $message = new Message();
                        try {

                            SwitchableEditor::getInstance()->addScript($name, $enabled, $visibility, $icon, $rooms, array(), $onCommand, $offCommand, $allowedUsers);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1507) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.error.1507'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addScript.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('elementForm', $scriptForm);
                    }
                    break;
            }
        } else {

            //Typauswahl Anzeigen
            $elementTypeChooser = new ElementTypeChooser('type');
            $elementTypeChooser->addId('shc-view-switchableManagement-typeChooser');
            $tpl->assign('elementTypeChooser', $elementTypeChooser);
        }

        //Template ausgeben
        $this->data = $tpl->fetchString('addelementform.html');
    }

}