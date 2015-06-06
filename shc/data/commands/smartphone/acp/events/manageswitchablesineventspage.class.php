<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Condition\Conditions\FirstLoopCondition;
use SHC\Condition\Conditions\HolidaysCondition;
use SHC\Condition\Conditions\InputHighCondition;
use SHC\Condition\Conditions\InputLowCondition;
use SHC\Condition\Conditions\UserNotAtHomeCondition;
use SHC\Core\SHC;
use SHC\Event\EventEditor;
use RWF\Form\FormElements\Select;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\Condition;
use SHC\Condition\ConditionEditor;
use SHC\Condition\Conditions\DateCondition;
use SHC\Condition\Conditions\DayOfWeekCondition;
use SHC\Condition\Conditions\FileExistsCondition;
use SHC\Condition\Conditions\HumidityGreaterThanCondition;
use SHC\Condition\Conditions\HumidityLowerThanCondition;
use SHC\Condition\Conditions\LightIntensityGreaterThanCondition;
use SHC\Condition\Conditions\LightIntensityLowerThanCondition;
use SHC\Condition\Conditions\MoistureGreaterThanCondition;
use SHC\Condition\Conditions\MoistureLowerThanCondition;
use SHC\Condition\Conditions\NobodyAtHomeCondition;
use SHC\Condition\Conditions\SunriseSunsetCondition;
use SHC\Condition\Conditions\SunsetSunriseCondition;
use SHC\Condition\Conditions\TemperatureGreaterThanCondition;
use SHC\Condition\Conditions\TemperatureLowerThanCondition;
use SHC\Condition\Conditions\TimeOfDayCondition;
use SHC\Condition\Conditions\UserAtHomeCondition;
use SHC\Event\Event;
use SHC\Form\FormElements\SwitchCommandChooser;
use SHC\Switchable\AbstractSwitchable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\ArduinoOutput;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * Zeigt eine Liste mit allen Schaltservern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ManageSwitchablesInEventsPage extends PageCommand {

    protected $template = 'manageswitchablesinevents.html';

    protected $premission = 'shc.acp.eventsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'eventmanagement', 'conditionmanagement', 'acpindex', 'form', 'index');

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
        if(RWF::getSession()->getMessage() != null) {
            $tpl->assign('message', RWF::getSession()->getMessage());
            RWF::getSession()->removeMessage();
        }

        //Ereignis Objekt laden
        $eventId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $event = EventEditor::getInstance()->getEventById($eventId);

        if($event instanceof Event) {

            //Formularfelder erstellen

            //Bedingungen
            $conditionChooser = new Select('condition');
            $values = array();
            foreach(ConditionEditor::getInstance()->listConditions(ConditionEditor::SORT_BY_NAME) as $condition) {

                //pruefen ob Bedingung schon registriert
                $found = false;
                foreach($event->listConditions() as $compareCondition) {

                    if($compareCondition == $condition) {

                        $found = true;
                        break;
                    }
                }
                if($found == true) {

                    //wenn schon registriert Bedingung ueberspringen
                    continue;
                }

                $type = '';
                RWF::getLanguage()->disableAutoHtmlEndocde();
                if($condition instanceof HumidityGreaterThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.HumidityGreaterThanCondition');
                } elseif($condition instanceof HumidityLowerThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.HumidityLowerThanCondition');
                } elseif($condition instanceof LightIntensityGreaterThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.LightIntensityGreaterThanCondition');
                } elseif($condition instanceof LightIntensityLowerThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.LightIntensityLowerThanCondition');
                } elseif($condition instanceof MoistureGreaterThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.MoistureGreaterThanCondition');
                } elseif($condition instanceof MoistureLowerThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.MoistureLowerThanCondition');
                } elseif($condition instanceof TemperatureGreaterThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.TemperatureGreaterThanCondition');
                } elseif($condition instanceof TemperatureLowerThanCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.TemperatureLowerThanCondition');
                } elseif($condition instanceof NobodyAtHomeCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.NobodyAtHomeCondition');
                } elseif($condition instanceof UserAtHomeCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.UserAtHomeCondition');
                } elseif($condition instanceof UserNotAtHomeCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.UserNotAtHomeCondition');
                } elseif($condition instanceof DateCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.DateCondition');
                } elseif($condition instanceof DayOfWeekCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.DayOfWeekCondition');
                } elseif($condition instanceof TimeOfDayCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.TimeOfDayCondition');
                } elseif($condition instanceof SunriseSunsetCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.SunriseSunsetCondition');
                } elseif($condition instanceof SunsetSunriseCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.SunsetSunriseCondition');
                } elseif($condition instanceof FileExistsCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.FileExistsCondition');
                } elseif($condition instanceof HolidaysCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.HolidaysCondition');
                } elseif($condition instanceof InputHighCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.InputHighCondition');
                } elseif($condition instanceof InputLowCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.InputLowCondition');
                } elseif($condition instanceof FirstLoopCondition) {

                    $type = RWF::getLanguage()->get('acp.conditionManagement.condition.FirstLoopCondition');
                }
                RWF::getLanguage()->enableAutoHtmlEndocde();
                $values[$condition->getId()] = $condition->getName() .' ('. $type .')';
            }
            $conditionChooser->setValues($values);

            //schaltbare Elemente
            $elementChooser = new Select('element');
            $values = array();
            foreach(SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME) as $switchableElement) {

                if($switchableElement instanceof Switchable) {

                    //pruefen ob Element schon registriert
                    $found = false;
                    foreach($event->listSwitchables() as $switchable) {

                        if($switchable['object'] == $switchableElement) {

                            $found = true;
                            break;
                        }
                    }
                    if($found == true) {

                        //wenn schon registriert Element ueberspringen
                        continue;
                    }

                    $type = '';
                    RWF::getLanguage()->disableAutoHtmlEndocde();
                    if($switchableElement instanceof ArduinoOutput) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.arduinoOutput');
                    } elseif($switchableElement instanceof RadioSocket) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.radiosocket');
                    } elseif($switchableElement instanceof RpiGpioOutput) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.rpiGpioOutput');
                    } elseif($switchableElement instanceof WakeOnLan) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.wakeOnLan');
                    } elseif($switchableElement instanceof Activity) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.activity');
                    } elseif($switchableElement instanceof Countdown) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.countdown');
                    } elseif($switchableElement instanceof Reboot) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.reboot');
                    } elseif($switchableElement instanceof Shutdown) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.shutdown');
                    } elseif($switchableElement instanceof Script) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.script');
                    } elseif($switchableElement instanceof AvmSocket) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.avmSocket');
                    } elseif($switchableElement instanceof FritzBox) {

                        $type = RWF::getLanguage()->get('acp.switchableManagement.element.fritzBox');
                    }
                    RWF::getLanguage()->enableAutoHtmlEndocde();
                    $values[$switchableElement->getId()] = $switchableElement->getName() .' ('. $type .') ['. $switchableElement->getNamedRoomList(true) .']';
                }
            }
            $elementChooser->setValues($values);

            //Schaltbefehl
            $switchCommand = new SwitchCommandChooser('switchCommand');

            //Elemente Liste Template Anzeigen
            $tpl->assign('event', $event);
            $tpl->assign('conditionChooser', $conditionChooser);
            $tpl->assign('elementChooser', $elementChooser);
            $tpl->assign('switchCommand', $switchCommand);
            $tpl->assign('elementList', $event->listSwitchables());
            $tpl->assign('conditionList', $event->listConditions());
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.eventsManagement.form.error.id')));
            return;
        }
    }

}