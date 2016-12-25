<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\ExtendetSwitchPointForm;
use SHC\Form\Forms\SimpleSwitchPointForm;
use SHC\Form\Forms\UserForm;
use SHC\Timer\SwitchPoint;
use SHC\Timer\SwitchPointEditor;

/**
 * bearbeitet einen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSwitchPointFormPage extends PageCommand {

    protected $template = 'editswitchpointform.html';

    protected $requiredPremission = 'shc.acp.switchpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchpointsmanagment', 'acpindex');

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
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listswitchpoints');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Schaltpunkt Objekt laden
        $switchPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);

        //pruefen ob der Schaltpunkt existiert
        if (!$switchPoint instanceof SwitchPoint) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id')));
            return;
        }

        //pruefen ob einfaches oder erweitertes Formular noetig
        $extendetForm = false;
        //erweitertes Formula angefordert
        if (RWF::getRequest()->issetParam('type', Request::GET) && RWF::getRequest()->getParam('type', Request::GET, DataTypeUtil::STRING) == 'extendet') {

            $extendetForm = true;
        }

        //automatisch erkennen
        if ($extendetForm === false) {

            $days = implode(',', $switchPoint->getDay());
            if ($days != '*' && $days != 'mon,tue,wed,thu,fri' && $days != 'sat,sun') {

                $extendetForm = true;
            }

            //Jahr
            $year = $switchPoint->getYear();
            if ($year[0] != '*') {

                $extendetForm = true;
            }

            //Monat
            $month = $switchPoint->getMonth();
            if ($month[0] != '*') {

                $extendetForm = true;
            }

            //Uhrzeit
            $hour = $switchPoint->getHour();
            if (count($hour) != 1 || $hour[0] == '*') {

                $extendetForm = true;
            }
            $min = $switchPoint->getMinute();
            if (count($min) != 1 || $min[0] == '*') {

                $extendetForm = true;
            }
        }

        //Formular erstellen
        if($extendetForm) {

            $switchPointForm = new ExtendetSwitchPointForm($switchPoint);
            $switchPointForm->setAction('index.php?app=shc&m&page=editswitchpointform&type=extendet&id='. $switchPoint->getId());
            $tpl->assign('type', 'extendet');
        } else {

            $switchPointForm = new SimpleSwitchPointForm($switchPoint);
            $switchPointForm->setAction('index.php?app=shc&m&page=editswitchpointform&id='. $switchPoint->getId());
            $tpl->assign('type', 'simple');
        }
        $switchPointForm->addId('shc-view-form-editSwitchPoint');
        $switchPointForm->setView(UserForm::SMARTPHONE_VIEW);

        if($switchPointForm->isSubmitted() && $switchPointForm->validate()) {

            //Speichern
            $name = $switchPointForm->getElementByName('name')->getValue();
            $command = $switchPointForm->getElementByName('command')->getValue();
            $enabled = $switchPointForm->getElementByName('enabled')->getValue();

            $message = new Message();
            if ($switchPointForm instanceof SimpleSwitchPointForm) {

                //Einfaches Formular speichern
                $dayOfWeek = $switchPointForm->getElementByName('daysOfWeek')->getValue();
                $dayOfWeekValue = array();
                if ($dayOfWeek == 1) {
                    $dayOfWeekValue = array('*');
                } elseif ($dayOfWeek == 2) {
                    $dayOfWeekValue = array('mon', 'tue', 'wed', 'thu', 'fri');
                } elseif ($dayOfWeek == 3) {
                    $dayOfWeekValue = array('sat', 'sun');
                }
                $hour = $switchPointForm->getElementByName('hour')->getValue();
                $minute = $switchPointForm->getElementByName('Minute')->getValue();

                try {

                    SwitchPointEditor::getInstance()->editSwitchPoint($switchPointId, $name, $enabled, $command, array(), array('*'), array('*'), array('*'), $dayOfWeekValue, array($hour), array($minute));
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.success.switchPoint'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1503) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.error.1503'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchpoints');
                $this->response->setBody('');
                $this->template = '';
            } elseif ($switchPointForm instanceof ExtendetSwitchPointForm) {

                //Erweitertes Formular Speichern
                $conditions = $switchPointForm->getElementByName('conditions')->getValues();
                $year = $switchPointForm->getElementByName('year')->getValues();
                $month = $switchPointForm->getElementByName('month')->getValues();
                $day = $switchPointForm->getElementByName('day')->getValues();
                $hour = $switchPointForm->getElementByName('hour')->getValues();
                $minute = $switchPointForm->getElementByName('Minute')->getValues();

                try {

                    SwitchPointEditor::getInstance()->editSwitchPoint($switchPointId, $name, $enabled, $command, $conditions, $year, $month, array('*'), $day, $hour, $minute);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.success.switchPoint'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1503) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.error.1503'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.switchpointsManagment.form.switchPoint.error'));
                    }
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listswitchpoints');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('switchPoint', $switchPoint);
            $tpl->assign('switchPointForm', $switchPointForm);
        }
    }

}