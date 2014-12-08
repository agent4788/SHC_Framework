<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Event\EventEditor;
use SHC\Form\FormElements\EventTypeChooser;
use SHC\Form\Forms\HumidityEventForm;
use SHC\Form\Forms\InputEventForm;
use SHC\Form\Forms\LightIntensityEventForm;
use SHC\Form\Forms\MoistureEventForm;
use SHC\Form\Forms\SunriseEventForm;
use SHC\Form\Forms\TemperatureEventForm;
use SHC\Form\Forms\UserEventForm;

/**
 * erstellt ein neues Ereignis
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddEventFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.eventsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('eventmanagement', 'acpindex', 'form');

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

                case EventEditor::EVENT_HUMIDITY_CLIMB:
                case EventEditor::EVENT_HUMIDITY_FALLS:

                    //Luftfeuchte Formular
                    $eventForm = new HumidityEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_HUMIDITY_CLIMB) {

                                EventEditor::getInstance()->addHumidityClimbOverEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } elseif ($type == EventEditor::EVENT_HUMIDITY_FALLS) {

                                EventEditor::getInstance()->addHumidityFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_INPUT_HIGH:
                case EventEditor::EVENT_INPUT_LOW:

                    //Eingangs Formular
                    $eventForm = new InputEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();
                        $inputs = $eventForm->getElementByName('inputs')->getValues();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_INPUT_HIGH) {

                                EventEditor::getInstance()->addInputHighEvent($name, $enabled, $inputs, $interval, $conditions);
                            } elseif ($type == EventEditor::EVENT_INPUT_LOW) {

                                EventEditor::getInstance()->addInputLowEvent($name, $enabled, $inputs, $interval, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_LIGHTINTENSITY_CLIMB:
                case EventEditor::EVENT_LIGHTINTENSITY_FALLS:

                    //Lichstaerke Formular
                    $eventForm = new LightIntensityEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_LIGHTINTENSITY_CLIMB) {

                                EventEditor::getInstance()->addLightIntensityClimbOverEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } elseif ($type == EventEditor::EVENT_LIGHTINTENSITY_FALLS) {

                                EventEditor::getInstance()->addLightIntensityFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_MOISTURE_CLIMB:
                case EventEditor::EVENT_MOISTURE_FALLS:

                    //Feuchtigkeit Formular
                    $eventForm = new MoistureEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_MOISTURE_CLIMB) {

                                EventEditor::getInstance()->addMoistureClimbOverEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } elseif ($type == EventEditor::EVENT_MOISTURE_FALLS) {

                                EventEditor::getInstance()->addMoistureFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_TEMPERATURE_CLIMB:
                case EventEditor::EVENT_TEMPERATURE_FALLS:

                    //Temperatur Formular
                    $eventForm = new TemperatureEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_TEMPERATURE_CLIMB) {

                                EventEditor::getInstance()->addTemperatureClimbOverEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } elseif ($type == EventEditor::EVENT_TEMPERATURE_FALLS) {

                                EventEditor::getInstance()->addTemperatureFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_USER_COMES_HOME:
                case EventEditor::EVENT_USER_LEAVE_HOME:

                    //Benutzer Formular
                    $eventForm = new UserEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();
                        $users = $eventForm->getElementByName('users')->getValues();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_USER_COMES_HOME) {

                                EventEditor::getInstance()->addUserComesHomeEvent($name, $enabled, $users, $interval, $conditions);
                            } elseif ($type == EventEditor::EVENT_USER_LEAVE_HOME) {

                                EventEditor::getInstance()->addUserLeavesHomeEvent($name, $enabled, $users, $interval, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_SUNRISE:
                case EventEditor::EVENT_SUNSET:

                    //Sonnenauf- und -ntergang
                    $eventForm = new SunriseEventForm();
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        $conditions = $eventForm->getElementByName('conditions')->getValues();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_SUNRISE) {

                                EventEditor::getInstance()->addSunriseEvent($name, $enabled, $conditions);
                            } elseif ($type == EventEditor::EVENT_SUNSET) {

                                EventEditor::getInstance()->addSunsetEvent($name, $enabled, $conditions);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.addEvent'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.event.error'));
                            }
                        }
                        $tpl->assign('message', $message);
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
            }

        } else {

            //Typauswahl Anzeigen
            $elementTypeChooser = new EventTypeChooser('type');
            $tpl->assign('eventTypeChooser', $elementTypeChooser);
        }

        //Formular ausgeben
        $this->data = $tpl->fetchString('addeventform.html');
    }

}