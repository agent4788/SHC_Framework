<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Room\RoomEditor;
use SHC\Sensor\Sensor;
use SHC\Sensor\SensorPointEditor;
use SHC\Switchable\Element;
use SHC\Switchable\SwitchableEditor;
use SHC\View\Room\ViewHelperBox;
use SHC\View\Room\ViewHelperEditor;


/**
 * Zeigt eine Liste mit allen Elementen an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ListSwitchablesAjax extends AjaxCommand {

    protected $premission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('switchablemanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        //Template Objekt holen
        $tpl = RWF::getTemplate();

        //Sortierung speichern
        $r = $this->request;
        if($r->issetParam('req', Request::GET) && $r->getParam('req', Request::GET, DataTypeUtil::STRING) == 'saveorder') {

            //Meldung
            $message = new Message();

            //eingabedaten Pruefen
            //schalt und lesbare Elemente
            $switchableOrder = $r->getParam('switchableOrder', Request::POST);
            $switchableFilteredOrder = array();
            $valid = true;
            if(count($switchableOrder)) {
                foreach ($switchableOrder as $id => $orderId) {

                    $switchable = SwitchableEditor::getInstance()->getElementById($id);
                    if ($switchable instanceof Element) {

                        $switchableFilteredOrder[$id] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
                    } else {

                        //Fehlerhafte Eingaben
                        $valid = false;
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                        break;
                    }
                }
            }
            //Sensoren
            $sensorOrder = $r->getParam('sensorOrder', Request::POST);
            $sensorFilteredOrder = array();
            if(count($sensorOrder)) {
                foreach ($sensorOrder as $id => $orderId) {

                    $sensor = SensorPointEditor::getInstance()->getSensorById($id);
                    if ($sensor instanceof Sensor) {

                        $sensorFilteredOrder[$id] = DataTypeUtil::convert($orderId, DataTypeUtil::INTEGER);
                    } else {

                        //Fehlerhafte Eingaben
                        $valid = false;
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('form.message.typedWrong'));
                        break;
                    }
                }
            }
            //Boxen
            $boxOrder = $r->getParam('boxOrder', Request::POST);
            $boxFilteredOrder = array();
            if(count($sensorOrder)) {
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
                    //Daten neu einlesen
                    SwitchableEditor::getInstance()->loadData();
                    SensorPointEditor::getInstance()->loadData();
                    ViewHelperEditor::getInstance()->loadData();
                    //Erfolgsmeldung
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.success.order'));
                } catch (\Exception $e) {

                    //Fehler beim speichern
                    $message->setType(Message::ERROR);
                    $message->setMessage(RWF::getLanguage()->get('acp.switchableManagement.form.error.order'));
                }
            }
            $tpl->assign('message', $message);
        }

        //Schalbare Elemente anzeigen
        $tpl->assign('roomList', RoomEditor::getInstance()->listRooms(RoomEditor::SORT_BY_ORDER_ID));
        $tpl->assign('viewHelperEditor', ViewHelperEditor::getInstance());
        $this->data = $tpl->fetchString('listswitchables.html');
    }

}