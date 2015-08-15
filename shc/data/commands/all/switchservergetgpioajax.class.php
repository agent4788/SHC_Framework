<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Runtime\RaspberryPi;
use RWF\Util\DataTypeUtil;
use SHC\Arduino\Arduino;
use SHC\SwitchServer\SwitchServerEditor;

/**
 * Listet die Anzahl der I/O Pins fuer einen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SwitchServerGetGpioAjax extends AjaxCommand {

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Schaltserver Objekt laden
        $switchServerId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $switchServer = SwitchServerEditor::getInstance()->getSwitchServerById($switchServerId);

        $model = $switchServer->getModel();
        $html = '';
        if($model == RaspberryPi::MODEL_A || $model == RaspberryPi::MODEL_B) {

            //Pins 0 - 20
            foreach(range(0, 20) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        } elseif($model == RaspberryPi::MODEL_A_PLUS || $model == RaspberryPi::MODEL_B_PLUS || $model == RaspberryPi::MODEL_2_B) {

            //Pins 0 - 29
            $html = '';
            foreach(range(0, 29) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        } elseif($model == RaspberryPi::MODEL_COMPUTE_MODULE) {

            //Pins 0 - 100
            $html = '';
            foreach(range(0, 100) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        } elseif($model == Arduino::PRO_MINI || $model == Arduino::NANO || $model == Arduino::UNO) {

            //Pins 0 - 13
            $html = '';
            foreach(range(0, 13) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        } elseif($model == Arduino::MEGA || $model == Arduino::DUE) {

            //Pins 0 - 53
            $html = '';
            foreach(range(0, 53) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        } elseif($model == Arduino::ESP8266_01) {

            //Pins 2 - 3
            $html = '';
            foreach(range(2, 3) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        } elseif($model == Arduino::ESP8266_12) {

            //Pins 0 - 15
            $html = '';
            foreach(range(0, 15) as $i) {

                $html .= '<option value="'. $i .'">'. $i .'</option>';
            }
        }

        $this->data = $html;
    }
}