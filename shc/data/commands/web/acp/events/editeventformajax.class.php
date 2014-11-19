<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Event\EventEditor;
use SHC\Event\Events\HumidityClimbOver;
use SHC\Event\Events\HumidityFallsBelow;
use SHC\Event\Events\InputHigh;
use SHC\Event\Events\InputLow;
use SHC\Event\Events\LightIntensityClimbOver;
use SHC\Event\Events\LightIntensityFallsBelow;
use SHC\Event\Events\MoistureClimbOver;
use SHC\Event\Events\MoistureFallsBelow;
use SHC\Event\Events\TemperatureClimbOver;
use SHC\Event\Events\TemperatureFallsBelow;
use SHC\Event\Events\UserComesHome;
use SHC\Event\Events\UserLeavesHome;
use SHC\Form\Forms\HumidityEventForm;
use SHC\Form\Forms\InputEventForm;
use SHC\Form\Forms\LightIntensityEventForm;
use SHC\Form\Forms\MoistureEventForm;
use SHC\Form\Forms\TemperatureEventForm;
use SHC\Form\Forms\UserEventForm;

/**
 * bearbeitet ein Ereignis
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditEventFormAjax extends AjaxCommand
{

    protected $premission = 'shc.acp.eventsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('eventmanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Ereignis Objekt laden
        $eventId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $event = EventEditor::getInstance()->getEventById($eventId);

        if($event instanceof HumidityClimbOver) {

            //Luftfeuchte steigt
            $eventForm = new HumidityEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editHumidityClimbOverEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof HumidityFallsBelow) {

            //Luftfeuchte faellt
            $eventForm = new HumidityEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editHumidityFallsBelowEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof InputHigh) {

            //Eingangs 0 => 1
            $eventForm = new InputEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $inputs = $eventForm->getElementByName('inputs')->getValues();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editInputHighEvent($eventId, $name, $enabled, $inputs, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof InputLow) {

            //Eingangs 1 => 0
            $eventForm = new InputEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $inputs = $eventForm->getElementByName('inputs')->getValues();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editInputLowEvent($eventId, $name, $enabled, $inputs, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof LightIntensityClimbOver) {

            //Lichstaerke steigt
            $eventForm = new LightIntensityEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editLightIntensityClimbOverEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof LightIntensityFallsBelow) {

            //Lichstaerke faellt
            $eventForm = new LightIntensityEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editLightIntensityFallsBelowEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof MoistureClimbOver) {

            //Feuchtigkeit steigt
            $eventForm = new MoistureEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editMoistureClimbOverEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof MoistureFallsBelow) {

            //Feuchtigkeit faellt
            $eventForm = new MoistureEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editMoistureFallsBelowEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof TemperatureClimbOver) {

            //Temperatur steigt
            $eventForm = new TemperatureEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editTemperatureClimbOverEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof TemperatureFallsBelow) {

            //Temperatur faellt
            $eventForm = new TemperatureEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $sensors = $eventForm->getElementByName('sensors')->getValues();
                $limit = $eventForm->getElementByName('limit')->getValue();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editTemperatureFallsBelowEvent($eventId, $name, $enabled, $sensors, $limit, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof UserComesHome) {

            //Benutzer kommt nach Hause
            $eventForm = new UserEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $users = $eventForm->getElementByName('users')->getValues();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editUserComesHomeEvent($eventId, $name, $enabled, $users, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } elseif($event instanceof UserLeavesHome) {

            //Benutzer verlaesst das Haus
            $eventForm = new UserEventForm($event);
            $eventForm->addId('shc-view-form-editEvent');

            if($eventForm->isSubmitted() && $eventForm->validate()) {

                //Werte vorbereiten
                $name = $eventForm->getElementByName('name')->getValue();
                $enabled = $eventForm->getElementByName('enabled')->getValue();
                $users = $eventForm->getElementByName('users')->getValues();
                $interval = $eventForm->getElementByName('interval')->getValue();

                //speichern
                $message = new Message();
                try {

                    EventEditor::getInstance()->editUserLeavesHomeEvent($eventId, $name, $enabled, $users, $interval);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.eventsManagement.form.success.editEvent'));
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

                $tpl->assign('event', $event);
                $tpl->assign('eventForm', $eventForm);
            }
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.eventsManagement.form.error.id')));
            $this->data = $tpl->fetchString('editeventform.html');
            return;
        }

        //Template ausgeben
        $this->data = $tpl->fetchString('editeventform.html');
    }

}