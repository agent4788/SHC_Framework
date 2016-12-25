<?php

namespace SHC\Command\Web;

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

    protected $template = 'sensorpointform.html';

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

        //Header Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());

        //SensorPunkt Objekt laden
        $sensorPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);

        //pruefen ob der Sensorpunkt existiert
        if(!$sensorPoint instanceof SensorPoint) {

            SHC::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.id')));
            //Umleiten
            $this->response->addLocationHeader('index.php?app=shc&m&page=listsensorpoints');
            $this->response->setBody('');
            $this->template = '';
        }

        //Formular erstellen
        $sensorPointForm = new SensorPointForm($sensorPoint);
        $sensorPointForm->setAction('index.php?app=shc&page=editsensorpointform&id='. $sensorPoint->getId());
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
            $this->response->addLocationHeader('index.php?app=shc&page=listsensorpoints');
            $this->response->setBody('');
            $this->template = '';
        } else {

            $tpl->assign('sensorPointForm', $sensorPointForm);
        }
    }
}