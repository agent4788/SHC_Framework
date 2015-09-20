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
use SHC\Timer\SwitchPointEditor;

/**
 * erstellt einen neuen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AddSwitchPointFormPage extends PageCommand {

    protected $template = 'addswitchpointform.html';

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

        //Formular erstellen
        if(RWF::getRequest()->issetParam('type', Request::GET) && RWF::getRequest()->getParam('type', Request::GET, DataTypeUtil::STRING) == 'extendet') {

            $switchPointForm = new ExtendetSwitchPointForm();
            $switchPointForm->setAction('index.php?app=shc&m&page=addswitchpointform&type=extendet');
            $tpl->assign('type', 'extendet');
        } else {

            $switchPointForm = new SimpleSwitchPointForm();
            $switchPointForm->setAction('index.php?app=shc&m&page=addswitchpointform');
            $tpl->assign('type', 'simple');
        }
        $switchPointForm->setView(UserForm::SMARTPHONE_VIEW);
        $switchPointForm->addId('shc-view-form-addSwitchPoint');

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

                    SwitchPointEditor::getInstance()->addSwitchPoint($name, $enabled, $command, array(), array('*'), array('*'), array('*'), $dayOfWeekValue, array($hour), array($minute));
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

                    SwitchPointEditor::getInstance()->addSwitchPoint($name, $enabled, $command, $conditions, $year, $month, array('*'), $day, $hour, $minute);
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
            }
        } else {

            $tpl->assign('switchPointForm', $switchPointForm);
        }
    }

}