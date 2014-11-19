<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Form\Forms\ExtendetSwitchPointForm;
use SHC\Form\Forms\SimpleSwitchPointForm;
use SHC\Timer\SwitchPoint;
use SHC\Timer\SwitchPointEditor;

/**
 * bearbeitet einen Schaltpunkt
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSwitchPointFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchpointsmanagment', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Schaltpunkt Objekt laden
        $switchPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchPoint = SwitchPointEditor::getInstance()->getSwitchPointById($switchPointId);

        //pruefen ob der Schaltpunkt existiert
        if (!$switchPoint instanceof SwitchPoint) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.switchpointsManagment.form.error.id')));
            $this->data = $tpl->fetchString('editswitchpointform.html');
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
            $tpl->assign('type', 'extendet');
        } else {

            $switchPointForm = new SimpleSwitchPointForm($switchPoint);
            $tpl->assign('type', 'simple');
        }
        $switchPointForm->addId('shc-view-form-editSwitchPoint');

        if($switchPointForm->isSubmitted() && $switchPointForm->validate()) {

            //Speichern
            $name = $switchPointForm->getElementByName('name')->getValue();
            $command = $switchPointForm->getElementByName('command')->getValue();
            $enabled = $switchPointForm->getElementByName('enabled')->getValue();

            $message = new Message();
            if ($switchPointForm instanceof SimpleSwitchPointForm) {

                //Einfaches Formular speichern
                $dayOfWeek = $switchPointForm->getElementByName('daysOfWeek')->getValue();
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
            $tpl->assign('message', $message);
        } else {

            $tpl->assign('switchPoint', $switchPoint);
            $tpl->assign('switchPointForm', $switchPointForm);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('editswitchpointform.html');
    }

}