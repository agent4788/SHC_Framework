<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Event\EventEditor;
use SHC\Form\FormElements\EventTypeChooser;
use SHC\Form\Forms\Events\FileEventForm;
use SHC\Form\Forms\Events\HumidityEventForm;
use SHC\Form\Forms\Events\InputEventForm;
use SHC\Form\Forms\Events\LightIntensityEventForm;
use SHC\Form\Forms\Events\MoistureEventForm;
use SHC\Form\Forms\Events\SunriseEventForm;
use SHC\Form\Forms\Events\TemperatureEventForm;
use SHC\Form\Forms\Events\UserEventForm;
use SHC\Form\Forms\UserForm;

/**
 * erstellt einen neuen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddEventFormPage extends PageCommand {

    protected $template = 'addeventform.html';

    protected $requiredPremission = 'shc.acp.eventsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'eventmanagement', 'acpindex');

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
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listevents');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

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
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_HUMIDITY_CLIMB) {

                                EventEditor::getInstance()->addHumidityClimbOverEvent($name, $enabled, $sensors, $limit, $interval, array());
                            } elseif ($type == EventEditor::EVENT_HUMIDITY_FALLS) {

                                EventEditor::getInstance()->addHumidityFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_INPUT_HIGH:
                case EventEditor::EVENT_INPUT_LOW:

                    //Eingangs Formular
                    $eventForm = new InputEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $inputs = $eventForm->getElementByName('inputs')->getValues();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_INPUT_HIGH) {

                                EventEditor::getInstance()->addInputHighEvent($name, $enabled, $inputs, $interval, array());
                            } elseif ($type == EventEditor::EVENT_INPUT_LOW) {

                                EventEditor::getInstance()->addInputLowEvent($name, $enabled, $inputs, $interval, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_LIGHTINTENSITY_CLIMB:
                case EventEditor::EVENT_LIGHTINTENSITY_FALLS:

                    //Lichstaerke Formular
                    $eventForm = new LightIntensityEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_LIGHTINTENSITY_CLIMB) {

                                EventEditor::getInstance()->addLightIntensityClimbOverEvent($name, $enabled, $sensors, $limit, $interval, array());
                            } elseif ($type == EventEditor::EVENT_LIGHTINTENSITY_FALLS) {

                                EventEditor::getInstance()->addLightIntensityFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_MOISTURE_CLIMB:
                case EventEditor::EVENT_MOISTURE_FALLS:

                    //Feuchtigkeit Formular
                    $eventForm = new MoistureEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_MOISTURE_CLIMB) {

                                EventEditor::getInstance()->addMoistureClimbOverEvent($name, $enabled, $sensors, $limit, $interval, array());
                            } elseif ($type == EventEditor::EVENT_MOISTURE_FALLS) {

                                EventEditor::getInstance()->addMoistureFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_TEMPERATURE_CLIMB:
                case EventEditor::EVENT_TEMPERATURE_FALLS:

                    //Temperatur Formular
                    $eventForm = new TemperatureEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $sensors = $eventForm->getElementByName('sensors')->getValues();
                        $limit = $eventForm->getElementByName('limit')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_TEMPERATURE_CLIMB) {

                                EventEditor::getInstance()->addTemperatureClimbOverEvent($name, $enabled, $sensors, $limit, $interval, array());
                            } elseif ($type == EventEditor::EVENT_TEMPERATURE_FALLS) {

                                EventEditor::getInstance()->addTemperatureFallsBelowEvent($name, $enabled, $sensors, $limit, $interval, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_USER_COMES_HOME:
                case EventEditor::EVENT_USER_LEAVE_HOME:

                    //Benutzer Formular
                    $eventForm = new UserEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $users = $eventForm->getElementByName('users')->getValues();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_USER_COMES_HOME) {

                                EventEditor::getInstance()->addUserComesHomeEvent($name, $enabled, $users, $interval, array());
                            } elseif ($type == EventEditor::EVENT_USER_LEAVE_HOME) {

                                EventEditor::getInstance()->addUserLeavesHomeEvent($name, $enabled, $users, $interval, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;
                case EventEditor::EVENT_SUNRISE:
                case EventEditor::EVENT_SUNSET:

                    //Sonnenauf- und -ntergang
                    $eventForm = new SunriseEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_SUNRISE) {

                                EventEditor::getInstance()->addSunriseEvent($name, $enabled, array());
                            } elseif ($type == EventEditor::EVENT_SUNSET) {

                                EventEditor::getInstance()->addSunsetEvent($name, $enabled, array());
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('eventForm', $eventForm);
                    }
                    break;;
                case EventEditor::EVENT_FILE_CREATE:
                case EventEditor::EVENT_FILE_DELETE:

                    //Benutzer Formular
                    $eventForm = new FileEventForm();
                    $eventForm->setAction('index.php?app=shc&m&page=addeventform');
                    $eventForm->setView(UserForm::SMARTPHONE_VIEW);
                    $eventForm->addId('shc-view-form-addEvent');

                    if($eventForm->isSubmitted() && $eventForm->validate()) {

                        //Werte vorbereiten
                        $name = $eventForm->getElementByName('name')->getValue();
                        $enabled = $eventForm->getElementByName('enabled')->getValue();
                        //$conditions = $eventForm->getElementByName('conditions')->getValues();
                        $file = $eventForm->getElementByName('file')->getValue();
                        $interval = $eventForm->getElementByName('interval')->getValue();

                        //speichern
                        $message = new Message();
                        try {

                            if ($type == EventEditor::EVENT_FILE_CREATE) {

                                EventEditor::getInstance()->addFileCreateEvent($name, $enabled, $file, $interval);
                            } elseif ($type == EventEditor::EVENT_FILE_DELETE) {

                                EventEditor::getInstance()->addFileDeleteEvent($name, $enabled, $file, $interval);
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
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&m&page=listevents');
                        $this->response->setBody('');
                        $this->template = '';
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
    }

}