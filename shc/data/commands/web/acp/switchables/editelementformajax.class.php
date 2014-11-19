<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Form\Forms\ActivityForm;
use SHC\Form\Forms\CountdownForm;
use SHC\Form\Forms\RadiosocketForm;
use SHC\Form\Forms\RpiGpioInputForm;
use SHC\Form\Forms\RpiGpioOutputForm;
use SHC\Form\Forms\WolForm;
use SHC\Switchable\Readables\ArduinoInput;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * bearbeitet ein Element
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditElementFormAjax extends AjaxCommand {

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

        //Element Objekt laden
        $elementId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $element = SwitchableEditor::getInstance()->getElementById($elementId);

        //Formulare je nach Objekttyp erstellen
        if($element instanceof Activity) {

            //Aktivitaet
            $activityForm = new ActivityForm($element);
            $activityForm->addId('shc-view-form-editElement');

            if($activityForm->isSubmitted() && $activityForm->validate()) {

                //Speichern
                $name = $activityForm->getElementByName('name')->getValue();
                $icon = $activityForm->getElementByName('icon')->getValue();;
                $roomId = $activityForm->getElementByName('room')->getValue();
                $switchPoints = $activityForm->getElementByName('switchPoints')->getValues();
                $enabled = $activityForm->getElementByName('enabled')->getValue();
                $visibility = $activityForm->getElementByName('visibility')->getValue();
                $allowedUsers = $activityForm->getElementByName('allowedUsers')->getValues();

                $message = new Message();
                try {

                    SwitchableEditor::getInstance()->editAcrivity($elementId, $name, $enabled, $visibility, $icon, $roomId, null, $switchPoints, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editActivity.success'));
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

                //Formular anzeigen
                $tpl->assign('element', $element);
                $tpl->assign('elementForm', $activityForm);
            }
        } elseif($element instanceof ArduinoInput) {

            //nicht Implementiert
        } elseif($element instanceof ArduinoInput) {

            //nicht Implementiert
        } elseif($element instanceof Countdown) {

            //Countdown
            $countdownForm = new CountdownForm($element);
            $countdownForm->addId('shc-view-form-editElement');

            if($countdownForm->isSubmitted() && $countdownForm->validate()) {

                //Speichern
                $name = $countdownForm->getElementByName('name')->getValue();
                $icon = $countdownForm->getElementByName('icon')->getValue();;
                $roomId = $countdownForm->getElementByName('room')->getValue();
                $interval = $countdownForm->getElementByName('interval')->getValue();
                $switchPoints = $countdownForm->getElementByName('switchPoints')->getValues();
                $enabled = $countdownForm->getElementByName('enabled')->getValue();
                $visibility = $countdownForm->getElementByName('visibility')->getValue();
                $allowedUsers = $countdownForm->getElementByName('allowedUsers')->getValues();

                $message = new Message();
                try {

                    SwitchableEditor::getInstance()->editCountdown($elementId, $name, $enabled, $visibility, $icon, $roomId, null, $interval, $switchPoints, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editCountdown.success'));
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

                //Formular anzeigen
                $tpl->assign('element', $element);
                $tpl->assign('elementForm', $countdownForm);
            }
        } elseif($element instanceof RadioSocket) {

            //Funksteckdose
            $radiosocketForm = new RadiosocketForm($element);
            $radiosocketForm->addId('shc-view-form-editElement');

            if($radiosocketForm->isSubmitted() && $radiosocketForm->validate()) {

                //Speichern
                $name = $radiosocketForm->getElementByName('name')->getValue();
                $icon = $radiosocketForm->getElementByName('icon')->getValue();
                $roomId = $radiosocketForm->getElementByName('room')->getValue();
                $protocol = $radiosocketForm->getElementByName('protocol')->getValue();
                $systemCode = $radiosocketForm->getElementByName('systemCode')->getValue();
                $deviceCode = $radiosocketForm->getElementByName('deviceCode')->getValue();
                $continuous = $radiosocketForm->getElementByName('continuous')->getValue();
                $switchPoints = $radiosocketForm->getElementByName('switchPoints')->getValues();
                $enabled = $radiosocketForm->getElementByName('enabled')->getValue();
                $visibility = $radiosocketForm->getElementByName('visibility')->getValue();
                $allowedUsers = $radiosocketForm->getElementByName('allowedUsers')->getValues();

                $message = new Message();
                try {

                    SwitchableEditor::getInstance()->editRadioSocket($elementId, $name, $enabled, $visibility, $icon, $roomId, null, $protocol, $systemCode, $deviceCode, $continuous, $switchPoints, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editRadioSocket.success'));
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

                //Formular anzeigen
                $tpl->assign('element', $element);
                $tpl->assign('elementForm', $radiosocketForm);
            }
        } elseif($element instanceof RpiGpioInput) {

            //GPIO Eingang
            $gpioInputForm = new RpiGpioInputForm($element);
            $gpioInputForm->addId('shc-view-form-editElement');

            if($gpioInputForm->isSubmitted() && $gpioInputForm->validate()) {

                //Speichern
                $name = $gpioInputForm->getElementByName('name')->getValue();
                $icon = '';
                $roomId = $gpioInputForm->getElementByName('room')->getValue();
                $switchServer = $gpioInputForm->getElementByName('switchServer')->getValue();
                $gpioPin = $gpioInputForm->getElementByName('gpio')->getValue();
                $enabled = $gpioInputForm->getElementByName('enabled')->getValue();
                $visibility = $gpioInputForm->getElementByName('visibility')->getValue();
                $allowedUsers = $gpioInputForm->getElementByName('allowedUsers')->getValues();

                $message = new Message();
                try {

                    SwitchableEditor::getInstance()->editRpiGpioInput($elementId, $name, $enabled, $visibility, $icon, $roomId, null, $switchServer, $gpioPin, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editGpioOutput.success'));
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

                //Formular anzeigen
                $tpl->assign('element', $element);
                $tpl->assign('elementForm', $gpioInputForm);
            }
        } elseif($element instanceof RpiGpioOutput) {

            //GPIO Ausgang
            $gpioOutputForm = new RpiGpioOutputForm($element);
            $gpioOutputForm->addId('shc-view-form-editElement');

            if($gpioOutputForm->isSubmitted() && $gpioOutputForm->validate()) {

                //Speichern
                $name = $gpioOutputForm->getElementByName('name')->getValue();
                $icon = $gpioOutputForm->getElementByName('icon')->getValue();
                $roomId = $gpioOutputForm->getElementByName('room')->getValue();
                $switchServer = $gpioOutputForm->getElementByName('switchServer')->getValue();
                $gpioPin = $gpioOutputForm->getElementByName('gpio')->getValue();
                $switchPoints = $gpioOutputForm->getElementByName('switchPoints')->getValues();
                $enabled = $gpioOutputForm->getElementByName('enabled')->getValue();
                $visibility = $gpioOutputForm->getElementByName('visibility')->getValue();
                $allowedUsers = $gpioOutputForm->getElementByName('allowedUsers')->getValues();

                $message = new Message();
                try {

                    SwitchableEditor::getInstance()->editRpiGpioOutput($elementId, $name, $enabled, $visibility, $icon, $roomId, null, $switchServer, $gpioPin, $switchPoints, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editGpioOutput.success'));
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

                //Formular anzeigen
                $tpl->assign('element', $element);
                $tpl->assign('elementForm', $gpioOutputForm);
            }
        } elseif($element instanceof WakeOnLan) {

            //Wake On Lan
            $wolForm = new WolForm($element);
            $wolForm->addId('shc-view-form-editElement');

            if($wolForm->isSubmitted() && $wolForm->validate()) {

                //Speichern
                $name = $wolForm->getElementByName('name')->getValue();
                $icon = '';
                $roomId = $wolForm->getElementByName('room')->getValue();
                $mac = $wolForm->getElementByName('mac')->getValue();
                $ip = $wolForm->getElementByName('ip')->getValue();
                $switchPoints = $wolForm->getElementByName('switchPoints')->getValues();
                $enabled = $wolForm->getElementByName('enabled')->getValue();
                $visibility = $wolForm->getElementByName('visibility')->getValue();
                $allowedUsers = $wolForm->getElementByName('allowedUsers')->getValues();

                $message = new Message();
                try {

                    SwitchableEditor::getInstance()->editWakeOnLan($elementId, $name, $enabled, $visibility, $icon, $roomId, null, $mac, $ip, $switchPoints, $allowedUsers);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.editWol.success'));
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

                //Formular anzeigen
                $tpl->assign('element', $element);
                $tpl->assign('elementForm', $wolForm);
            }
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            $this->data = $tpl->fetchString('editelementform.html');
            return;
        }

        //Template ausgeben
        $this->data = $tpl->fetchString('editelementform.html');
    }

}