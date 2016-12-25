<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\ConditionEditor;
use SHC\Core\SHC;
use SHC\Form\FormElements\ConditionTypeChooser;
use SHC\Form\Forms\Conditions\CalendarWeekConditionForm;
use SHC\Form\Forms\Conditions\DateConditionForm;
use SHC\Form\Forms\Conditions\DayOfWeekConditionForm;
use SHC\Form\Forms\Conditions\FileExistsConditionForm;
use SHC\Form\Forms\Conditions\FirstLoopConditionForm;
use SHC\Form\Forms\Conditions\HolidayConditionForm;
use SHC\Form\Forms\Conditions\HumidityConditionForm;
use SHC\Form\Forms\Conditions\InputHighConditionForm;
use SHC\Form\Forms\Conditions\InputLowConditionForm;
use SHC\Form\Forms\Conditions\LightIntensityConditionForm;
use SHC\Form\Forms\Conditions\MoistureConditionForm;
use SHC\Form\Forms\Conditions\NobodyAtHomeConditionForm;
use SHC\Form\Forms\Conditions\SunriseSunsetConditionForm;
use SHC\Form\Forms\Conditions\SunsetSunriseConditionForm;
use SHC\Form\Forms\Conditions\SwitchableStateConditionForm;
use SHC\Form\Forms\Conditions\TemperatureConditionForm;
use SHC\Form\Forms\Conditions\TimeOfDayConditionForm;
use SHC\Form\Forms\Conditions\UserAtHomeConditionForm;
use SHC\Form\Forms\Conditions\UserNotAtHomeConditionForm;

/**
 * erstellt einen neuen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddConditionFormPage extends PageCommand {

    protected $template = 'addconditionform.html';

    protected $requiredPremission = 'shc.acp.conditionsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'conditionmanagement', 'switchablemanagement', 'acpindex');

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

        //Typ ermitteln
        if(RWF::getRequest()->issetParam('type', Request::GET) || RWF::getSession()->issetVar('type')) {

            //Neues Formular
            if(RWF::getSession()->issetVar('type')) {

                $type = RWF::getSession()->get('type');
            } else {

                $type = RWF::getRequest()->getParam('type', Request::GET, DataTypeUtil::INTEGER);
            }
            RWF::getSession()->set('type', $type);

            //Formular je nach Typ erstellen
            switch($type) {

                case 1: //Luftfeuchte groeßer als
                case 2: //Luftfeuchte kleiner als

                    //Luftfeuchte groeßer/kleiner als
                    $conditionForm = new HumidityConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $sensors = $conditionForm->getElementByName('sensors')->getValues();
                        $humidity = $conditionForm->getElementByName('humidity')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            if($type == 1) {

                                ConditionEditor::getInstance()->addHumidityGreaterThanCondition($name, $sensors, $humidity, $enabled);
                            } elseif($type == 2) {

                                ConditionEditor::getInstance()->addHumidityLowerThanCondition($name, $sensors, $humidity, $enabled);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.formError'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 3: //Lichstärke groeßer als
                case 4: //Lichtstärke kleiner als

                    //Lichtstaerke groeßer/kleiner als
                    $conditionForm = new LightIntensityConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $sensors = $conditionForm->getElementByName('sensors')->getValues();
                        $lightIntensity = $conditionForm->getElementByName('lightIntensity')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            if($type == 3) {

                                ConditionEditor::getInstance()->addLightIntensityGreaterThanCondition($name, $sensors, $lightIntensity, $enabled);
                            } elseif($type == 4) {

                                ConditionEditor::getInstance()->addLightIntensityLowerThanCondition($name, $sensors, $lightIntensity, $enabled);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.formError'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 5: //Feuchtigkeit groeßer als
                case 6: //Feuchtigkeit kleiner als

                    //Feuchtigkeit groeßer/kleiner als
                    $conditionForm = new MoistureConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $sensors = $conditionForm->getElementByName('sensors')->getValues();
                        $moisture = $conditionForm->getElementByName('moisture')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            if($type == 5) {

                                ConditionEditor::getInstance()->addMoistureGreaterThanCondition($name, $sensors, $moisture, $enabled);
                            } elseif($type == 6) {

                                ConditionEditor::getInstance()->addLMoistureLowerThanCondition($name, $sensors, $moisture, $enabled);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.formError'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 7: //Temperatur groeßer als
                case 8: //Temperatur kleiner als

                    //Temperatur groeßer/kleiner als
                    $conditionForm = new TemperatureConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $sensors = $conditionForm->getElementByName('sensors')->getValues();
                        $temperature = $conditionForm->getElementByName('temperature')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            if($type == 7) {

                                ConditionEditor::getInstance()->addTemperatureGreaterThanCondition($name, $sensors, $temperature, $enabled);
                            } elseif($type == 8) {

                                ConditionEditor::getInstance()->addTemperatureLowerThanCondition($name, $sensors, $temperature, $enabled);
                            } else {

                                //Typfehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.formError'));
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 9:

                    //Niemand zu Hause
                    $conditionForm = new NobodyAtHomeConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addNobodyAtHomeCondition($name, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 10:

                    //Benutzer zu Hause
                    $conditionForm = new UserAtHomeConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $users = $conditionForm->getElementByName('users')->getValues();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addUserAtHomeCondition($name, $users, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 11:

                    //Datumsbereich
                    $conditionForm = new DateConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    //Formular Validieren
                    $valid = true;
                    if($conditionForm->isSubmitted()) {

                        //Name
                        if(!$conditionForm->validateByName('name')) {

                            $valid = false;
                        }

                        //Aktiviert
                        if(!$conditionForm->validateByName('enabled')) {

                            $valid = false;
                        }

                        //Start Datum
                        if(!$conditionForm->validateByName('startDate')) {

                            $valid = false;
                        }
                        $startDate = $conditionForm->getElementByName('startDate');
                        $matches = array();
                        preg_match('#(\d\d)\-(\d\d)#', $startDate->getValue(), $matches);
                        if(!isset($matches[1]) || !isset($matches[2]) || (int) $matches[1] < 1 || (int) $matches[1] > 12 || (int) $matches[2] < 1 || (int) $matches[2] > 31) {

                            $conditionForm->markElementAsInvalid('startDate', RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.invalidDate', $startDate->getTitle()));
                            $valid = false;
                        }

                        //End Datum
                        if(!$conditionForm->validateByName('startDate')) {

                            $valid = false;
                        }
                        $endDate = $conditionForm->getElementByName('endDate');
                        $matches = array();
                        preg_match('#(\d\d)\-(\d\d)#', $endDate->getValue(), $matches);
                        if(!isset($matches[1]) || !isset($matches[2]) || (int) $matches[1] < 1 || (int) $matches[1] > 12 || (int) $matches[2] < 1 || (int) $matches[2] > 31) {

                            $conditionForm->markElementAsInvalid('endDate', RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.invalidDate', $endDate->getTitle()));
                            $valid = false;
                        }
                    }

                    if($conditionForm->isSubmitted() && $valid === true) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $startDate = $conditionForm->getElementByName('startDate')->getValue();
                        $endDate = $conditionForm->getElementByName('endDate')->getValue();


                        //Speichern
                        $message = new Message();
                        try {
                            ConditionEditor::getInstance()->addDateCondition($name, $startDate, $endDate, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch (\Exception $e) {

                            if ($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif ($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 12:

                    //Tag der Woche
                    $conditionForm = new DayOfWeekConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $startDay = $conditionForm->getElementByName('startDay')->getValue();
                        $endDay = $conditionForm->getElementByName('endDay')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addDayOfWeekCondition($name, $startDay, $endDay, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 13:

                    //Tageszeit
                    $conditionForm = new TimeOfDayConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $startHour = $conditionForm->getElementByName('startHour')->getValue();
                        $startMinute = $conditionForm->getElementByName('startMinute')->getValue();
                        $endHour = $conditionForm->getElementByName('endHour')->getValue();
                        $endMinute = $conditionForm->getElementByName('endMinute')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addTimeOfDayCondition($name, $startHour .':'. $startMinute, $endHour .':'. $endMinute, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 14:

                    //Tag
                    $conditionForm = new SunriseSunsetConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addSunriseSunsetCondition($name, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 15:

                    //Nacht
                    $conditionForm = new SunsetSunriseConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addSunsetSunriseCondition($name, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 16:

                    //Datei vorhanden/nicht vorhanden
                    $conditionForm = new FileExistsConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $path = $conditionForm->getElementByName('path')->getValue();
                        $wait = $conditionForm->getElementByName('wait')->getValue();
                        $delete = $conditionForm->getElementByName('delete')->getValue();
                        $invert = $conditionForm->getElementByName('invert')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addFileExistsCondition($name, $path, $invert, $wait, $delete, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 17:

                    //Feiertage
                    $conditionForm = new HolidayConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $holidays = $conditionForm->getElementByName('holidays')->getHolidays();
                        $invert = $conditionForm->getElementByName('invert')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addHolidaysCondition($name, $holidays, $enabled, $invert);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 18:

                    //Eingang "1"
                    $conditionForm = new InputHighConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $inputs = $conditionForm->getElementByName('inputs')->getValues();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addInputHighCondition($name, $inputs, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 19:

                    //Eingang "0"
                    $conditionForm = new InputLowConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $inputs = $conditionForm->getElementByName('inputs')->getValues();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addInputLowCondition($name, $inputs, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 20:

                    //erster Sheduler lauf
                    $conditionForm = new FirstLoopConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addFirstLoopCondition($name, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 21:

                    //Benutzer nicht zu Hause
                    $conditionForm = new UserNotAtHomeConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();
                        $users = $conditionForm->getElementByName('users')->getValues();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addUserNotAtHomeCondition($name, $users, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 22:

                    //gerade Kalenderwoche
                    $conditionForm = new CalendarWeekConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addJustCalendarWeekCondition($name, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 23:

                    //ungerade Kalenderwoche
                    $conditionForm = new CalendarWeekConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addOddCalendarWeekCondition($name, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 24:

                    //schaltbares Element "an"
                    $conditionForm = new SwitchableStateConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $switchables = $conditionForm->getElementByName('switchables')->getValues();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addSwitchableStateHighCondition($name, $switchables, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
                case 25:

                    //schaltbares Element "aus"
                    $conditionForm = new SwitchableStateConditionForm();
                    $conditionForm->setAction('index.php?app=shc&page=addconditionform');
                    $conditionForm->addId('shc-view-form-addCondition');

                    if($conditionForm->isSubmitted() && $conditionForm->validate()) {

                        //Werte vorbereiten
                        $name = $conditionForm->getElementByName('name')->getValue();
                        $switchables = $conditionForm->getElementByName('switchables')->getValues();
                        $enabled = $conditionForm->getElementByName('enabled')->getValue();

                        //Speichern
                        $message = new Message();
                        try {

                            ConditionEditor::getInstance()->addSwitchableStateLowCondition($name, $switchables, $enabled);
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                        } catch(\Exception $e) {

                            if($e->getCode() == 1502) {

                                //Name schon vergeben
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                            } elseif($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                            }
                        }
                        RWF::getSession()->setMessage($message);

                        //Umleiten
                        $this->response->addLocationHeader('index.php?app=shc&page=listconditions');
                        $this->response->setBody('');
                        $this->template = '';
                    } else {

                        $tpl->assign('conditionForm', $conditionForm);
                    }
                    break;
            }
        } else {

            //Typauswahl Anzeigen
            $elementTypeChooser = new ConditionTypeChooser('type');
            $tpl->assign('conditionTypeChooser', $elementTypeChooser);
        }
    }

}