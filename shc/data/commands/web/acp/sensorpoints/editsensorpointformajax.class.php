<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Form\Forms\SensorPointForm;
use SHC\Sensor\SensorPoint;
use SHC\Sensor\SensorPointEditor;

/**
 * bearbeitet einen Sensorpunkt
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditSensorPointFormAjax extends AjaxCommand {

    protected $premission = 'shc.acp.sensorpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('sensorpointsmanagement', 'form', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //SensorPunkt Objekt laden
        $sensorPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);

        //pruefen ob der Sensorpunkt existiert
        if(!$sensorPoint instanceof SensorPoint) {

            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.id')));
            $this->data = $tpl->fetchString('editsensorpointform.html');
            return;
        }

        //Formular erstellen
        $sensorPointForm = new SensorPointForm($sensorPoint);
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
            $tpl->assign('message', $message);
        } else {

            $tpl->assign('sensorPoint', $sensorPoint);
            $tpl->assign('sensorPointForm', $sensorPointForm);
        }

        //Template anzeigen
        $this->data = $tpl->fetchString('editsensorpointform.html');
    }

}