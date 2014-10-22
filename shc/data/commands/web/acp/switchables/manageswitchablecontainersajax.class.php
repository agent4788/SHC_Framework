<?php

namespace SHC\Command\Web;

//Imports
use RWF\Form\FormElements\Select;
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Switchable\AbstractSwitchable;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\ArduinoOutput;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\WakeOnLan;

/**
 * verwaltet die Elemente von Schaltbaren Containern
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ManageSwitchableContainersAjax extends AjaxCommand {

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

        if($element instanceof Activity || $element instanceof Countdown) {


            if(RWF::getRequest()->issetParam('command', Request::GET)) {

                //Befehl
                $command = RWF::getRequest()->getParam('command', Request::GET, DataTypeUtil::STRING);

                $message = new Message();
                if($command == 'addElement') {

                    //element hinzufuegen
                    $switchableElementId = RWF::getRequest()->getParam('element', Request::GET, DataTypeUtil::INTEGER);
                    $switchCommand = RWF::getRequest()->getParam('switchCommand', Request::GET, DataTypeUtil::INTEGER);

                    //Eingaben pruefen
                    $error = false;
                    $switchableElementObject = SwitchableEditor::getInstance()->getElementById($switchableElementId);
                    if (!$switchableElementObject instanceof Switchable) {

                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.id'));
                        $error = true;
                    }

                    if (!in_array($switchCommand, array('0', '1'))) {

                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.command'));
                        $error = true;
                    }

                    //Element hinzufuegen
                    if ($error === false) {

                        try {

                            //Speichern
                            if ($element instanceof Activity) {

                                SwitchableEditor::getInstance()->addSwitchableToActivity($element->getId(), $switchableElementId, $switchCommand);
                            } elseif ($element instanceof Countdown) {

                                SwitchableEditor::getInstance()->addSwitchableToCountdown($element->getId(), $switchableElementId, $switchCommand);
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.success'));
                            SwitchableEditor::getInstance()->loadData();
                            $element = SwitchableEditor::getInstance()->getElementById($elementId);
                        } catch (\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.error'));
                            }
                        }
                    }
                } elseif($command == 'toggle') {

                    //Befehl umkehren
                    $switchableElementId = RWF::getRequest()->getParam('element', Request::GET, DataTypeUtil::INTEGER);

                    //Eingaben pruefen
                    $error = false;
                    $switchableElementObject = SwitchableEditor::getInstance()->getElementById($switchableElementId);
                    if (!$switchableElementObject instanceof Switchable) {

                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.id'));
                        $error = true;
                    }

                    //Element hinzufuegen
                    if ($error === false) {

                        try {

                            //Speichern
                            $newCommand = AbstractSwitchable::STATE_OFF;
                            foreach($element->listSwitchables() as $switchable) {

                                if($switchable['object'] == $switchableElementObject) {

                                    if($switchable['command'] == AbstractSwitchable::STATE_ON) {

                                        $newCommand = AbstractSwitchable::STATE_OFF;
                                    } else {

                                        $newCommand = AbstractSwitchable::STATE_ON;
                                    }
                                }
                            }
                            if ($element instanceof Activity) {

                                SwitchableEditor::getInstance()->setActivitySwitchableCommand($element->getId(), $switchableElementId, $newCommand);
                            } elseif ($element instanceof Countdown) {

                                SwitchableEditor::getInstance()->addSwitchableToCountdown($element->getId(), $switchableElementId, $newCommand);
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.success'));
                            SwitchableEditor::getInstance()->loadData();
                            $element = SwitchableEditor::getInstance()->getElementById($elementId);
                        } catch (\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.addElementToActivity.error'));
                            }
                        }
                    }
                } elseif($command = 'delete') {

                    //element loeschen
                    $switchableElementId = RWF::getRequest()->getParam('element', Request::GET, DataTypeUtil::INTEGER);

                    //Eingaben pruefen
                    $error = false;
                    $switchableElementObject = SwitchableEditor::getInstance()->getElementById($switchableElementId);
                    if (!$switchableElementObject instanceof Switchable) {

                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.id'));
                        $error = true;
                    }

                    //Element hinzufuegen
                    if ($error === false) {

                        try {

                            //loeschen
                            if ($element instanceof Activity) {

                                SwitchableEditor::getInstance()->removeSwitchableFromActivity($element->getId(), $switchableElementId);
                            } elseif ($element instanceof Countdown) {

                                SwitchableEditor::getInstance()->removeSwitchableFromCountdown($element->getId(), $switchableElementId);
                            }
                            $message->setType(Message::SUCCESSFULLY);
                            $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteElementFromActivity.success'));
                            SwitchableEditor::getInstance()->loadData();
                            $element = SwitchableEditor::getInstance()->getElementById($elementId);
                        } catch (\Exception $e) {

                            if($e->getCode() == 1102) {

                                //fehlende Schreibrechte
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteElementFromActivity.error.1102'));
                            } else {

                                //Allgemeiner Fehler
                                $message->setType(Message::ERROR);
                                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.deleteElementFromActivity.error'));
                            }
                        }
                    }
                }

                $tpl->assign('message', $message);
            }

                //Formularfelder erstellen
                $elementChooser = new Select('element');
                $values = array();
                foreach(SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_BY_NAME) as $switchableElement) {

                    if(
                        $switchableElement instanceof ArduinoOutput
                        || $switchableElement instanceof RadioSocket
                        || $switchableElement instanceof RpiGpioOutput
                        || $switchableElement instanceof WakeOnLan
                    ) {

                        //pruefen ob Element schon registriert
                        $found = false;
                        foreach($element->listSwitchables() as $switchable) {

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
                        if($switchableElement instanceof ArduinoOutput) {

                            $type = RWF::getLanguage()->get('acp.switchableManagement.element.arduinoOutput');
                        } elseif($switchableElement instanceof RadioSocket) {

                            $type = RWF::getLanguage()->get('acp.switchableManagement.element.radiosocket');
                        } elseif($switchableElement instanceof RpiGpioOutput) {

                            $type = RWF::getLanguage()->get('acp.switchableManagement.element.rpiGpioOutput');
                        } elseif($switchableElement instanceof WakeOnLan) {

                            $type = RWF::getLanguage()->get('acp.switchableManagement.element.wakeOnLan');
                        }
                        $values[$switchableElement->getId()] = $switchableElement->getName() .' ('. $type .')';
                    }
                }
                $elementChooser->setValues($values);

                //Schaltbefehl
                $switchCommand = new Select('switchCommand');
                $switchCommand->setValues(array(
                    '1' => RWF::getLanguage()->get('global.on'),
                    '0' => RWF::getLanguage()->get('global.off')
                ));

                //Elemente Liste Template Anzeigen
                $tpl->assign('SwitchableContainer', $element);
                $tpl->assign('elementChooser', $elementChooser);
                $tpl->assign('switchCommand', $switchCommand);
                $tpl->assign('elementList', $element->listSwitchables());
                $this->data = $tpl->fetchString('manageswitchablecontainers.html');

        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchableManagement.form.error.id')));
            $this->data = $tpl->fetchString('manageswitchablecontainers.html');
            return;
        }
    }

}