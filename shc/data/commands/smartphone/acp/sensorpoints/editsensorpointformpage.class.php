<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Core\SHC;
use SHC\Form\Forms\SensorPointForm;
use SHC\Sensor\SensorPoint;
use SHC\Sensor\SensorPointEditor;

/**
 * Zeigt eine Liste mit allen Benutzern an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSensorPointFormPage extends PageCommand {

    protected $template = 'editsensorpointform.html';

    protected $requiredPremission = 'shc.acp.sensorpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'sensorpointsmanagement', 'acpindex');

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
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listsensorpoints');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //SensorPunkt Objekt laden
        $sensorPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);

        //pruefen ob der Sensorpunkt existiert
        if(!$sensorPoint instanceof SensorPoint) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.id')));
            return;
        }

        //Formular erstellen
        $sensorPointForm = new SensorPointForm($sensorPoint);
        $sensorPointForm->setView(SensorPointForm::SMARTPHONE_VIEW);
        $sensorPointForm->setAction('index.php?app=shc&m&page=editsensorpointform&id='. $sensorPoint->getId());
        $sensorPointForm->addId('shc-view-form-editSensorPoint');

        if($sensorPointForm->isSubmitted() && $sensorPointForm->validate() === true) {

            //Speichern
            $name = $sensorPointForm->getElementByName('name')->getValue();
            $warnLevel = $sensorPointForm->getElementByName('warnLevel')->getValue();

            $message = new Message();
            try {

                SensorPointEditor::getInstance()->editSensorPoint($sensorPointId, $name, true, $warnLevel);
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.success'));
            } catch(\Exception $e) {

                if($e->getCode() == 1507) {

                    //Raumname schon vergeben
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.1507'));
                }  elseif($e->getCode() == 1102) {

                    //fehlende Schreibrechte
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.1102'));
                } else {

                    //Allgemeiner Fehler
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.error'));
                }
            }
            RWF::getSession()->setMessage($message);

            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listsensorpoints');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('sensorPointForm', $sensorPointForm);
        }
    }
}