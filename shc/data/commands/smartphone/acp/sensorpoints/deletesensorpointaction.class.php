<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Sensor\SensorPoint;
use SHC\Sensor\SensorPointEditor;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DeleteSensorPointAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.sensorpointsManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc&m&page=listsensorpoints';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'sensorpointsmanagement', 'acpindex');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        //SensorPunkt Objekt laden
        $sensorPointId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $sensorPoint = SensorPointEditor::getInstance()->getSensorPointById($sensorPointId);

        //pruefen ob der Sensorpunkt existiert
        if(!$sensorPoint instanceof SensorPoint) {

            RWF::getSession()->setMessage(new Message(Message::ERROR, RWF::getLanguage()->get('acp.sensorpointsManagement.form.error.id')));
            return;
        }

        //Benutzer loeschen
        $message = new Message();
        try {

            SensorPointEditor::getInstance()->removeSensorPoint($sensorPointId);
            $message->setType(Message::SUCCESSFULLY);
            $message->setMessage(RWF::getLanguage()->get('acp.sensorpointsManagement.form.success.del'));
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
        RWF::getSession()->setMessage($message);
    }
}