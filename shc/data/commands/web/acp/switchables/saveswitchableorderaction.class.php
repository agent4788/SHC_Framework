<?php

namespace SHC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\ActionCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Sensor\Sensor;
use SHC\Sensor\SensorPointEditor;
use SHC\Switchable\Element;
use SHC\Switchable\SwitchableEditor;
use SHC\View\Room\ViewHelperBox;
use SHC\View\Room\ViewHelperEditor;

/**
 * Speichert die Sortierung der Raeume
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SaveSwitchableOrderAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = '';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'form');

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        $r = $this->request;

        //Sortierung speichern
        //Meldung
        $message = new Message();

        //eingabedaten Pruefen
        //schalt und lesbare Elemente
        $switchableOrder = $r->getParam('switchableOrder', Request::POST);
        $switchableFilteredOrder = array();
        $valid = true;
        if(count($switchableOrder)) {

            foreach($switchableOrder as $roomId => $order) {

                foreach ($order as $id => $orderId) {

                    $switchable = SwitchableEditor::getInstance()->getElementById($id);
                    if ($switchable instanceof Element) {

                        $switchableFilteredOrder[$id][$roomId] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
                    } else {

                        //Fehlerhafte Eingaben
                        $valid = false;
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                        break;
                    }
                }
            }
        }

        //Sensoren
        $sensorOrder = $r->getParam('sensorOrder', Request::POST);
        $sensorFilteredOrder = array();
        if(count($sensorOrder)) {

            foreach($sensorOrder as $roomId => $order) {

                foreach ($order as $id => $orderId) {

                    $sensor = SensorPointEditor::getInstance()->getSensorById($id);
                    if ($sensor instanceof Sensor) {

                        $sensorFilteredOrder[$id][$roomId] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
                    } else {

                        //Fehlerhafte Eingaben
                        $valid = false;
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                        break;
                    }
                }
            }
        }

        //Boxen
        $boxOrder = $r->getParam('boxOrder', Request::POST);
        $boxFilteredOrder = array();
        if(count($boxOrder)) {

            foreach ($boxOrder as $id => $orderId) {

                $box = ViewHelperEditor::getInstance()->getBoxById($id);
                if ($box instanceof ViewHelperBox) {

                    $boxFilteredOrder[$id] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
                } else {

                    //Fehlerhafte Eingaben
                    $valid = false;
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                    break;
                }
            }
        }

        //Speichern
        if($valid === true) {

            try {

                //Sortierungen speichern
                SwitchableEditor::getInstance()->editOrder($switchableFilteredOrder);
                SensorPointEditor::getInstance()->editSensorOrder($sensorFilteredOrder);
                ViewHelperEditor::getInstance()->editBoxOrder($boxFilteredOrder);
                //Erfolgsmeldung
                $message->setType(Message::SUCCESSFULLY);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.success.order'));
            } catch (\Exception $e) {

                //Fehler beim speichern
                $message->setType(Message::ERROR);
                $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.order'));
            }
        }

        RWF::getSession()->setMessage($message);
        $this->location = 'index.php?app=shc&page=listswitchables';;
    }
}