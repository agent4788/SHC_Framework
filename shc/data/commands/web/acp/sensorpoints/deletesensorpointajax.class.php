<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Sensor\SensorPoint;
use SHC\Sensor\SensorPointEditor;


/**
 * loescht einen Sensorpunkt
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteSensorPointAjax extends AjaxCommand {

    protected $premission = 'shc.acp.sensorpointsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('sensorpointsmanagement');

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
            $this->data = $tpl->fetchString('deletesensorpoint.html');
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            SensorPointEditor::getInstance()->removeSensorPoint($sensorPointId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.success.del'));
            $message1 = new Message(Message::MESSAGE, RWF::getLanguage()->get('acp.sensorpointsManagement.form.success.del.info'));
            $tpl->assign('message1', $message1);
        } catch(\Exception $e) {

            if($e->getCode() == 1102) {

                //fehlende Schreibrechte
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.1102.del'));
            } else {

                //Allgemeiner Fehler
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.del'));
            }
        }
        $tpl->assign('message', $message);
        $this->data = $tpl->fetchString('deletesensorpoint.html');
    }

}