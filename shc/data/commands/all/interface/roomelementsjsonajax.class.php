<?php

namespace SHC\Command\All;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\User\UserEditor;
use RWF\Util\DataTypeUtil;
use SHC\Sensor\Sensor;
use SHC\Sensor\Sensors\AvmMeasuringSocket;
use SHC\Sensor\Sensors\BMP;
use SHC\Sensor\Sensors\DHT;
use SHC\Sensor\Sensors\DS18x20;
use SHC\Sensor\Sensors\Hygrometer;
use SHC\Sensor\Sensors\LDR;
use SHC\Sensor\Sensors\RainSensor;
use SHC\Switchable\AbstractSwitchable;
use SHC\Switchable\Readable;
use SHC\Switchable\Switchable;
use SHC\Switchable\Switchables\Activity;
use SHC\Switchable\Switchables\AvmSocket;
use SHC\Switchable\Switchables\Countdown;
use SHC\Switchable\Switchables\FritzBox;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\Reboot;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\Switchable\Switchables\Script;
use SHC\Switchable\Switchables\Shutdown;
use SHC\Switchable\Switchables\WakeOnLan;
use SHC\View\Room\ViewHelperBox;
use SHC\View\Room\ViewHelperEditor;

/**
 * gibt eine Liste mit den Elementen eines Raumes als JSON String zurueck
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2015, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RoomElementsJsonAjax extends AjaxCommand
{

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Raum ID einlesen
        $roomId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);

        //Daten vorbereiten
        $data = array();

        //Elemente laden
        $roomElements = ViewHelperEditor::getInstance()->getViewHelperForRoom($roomId)->listElementsOrdered();

        //Benutzeranmeldung
        $rwfUser = UserEditor::getInstance()->getGuest();
        if(RWF::getRequest()->issetParam('user', Request::GET) && RWF::getRequest()->issetParam('password', Request::GET)) {

            $userName = RWF::getRequest()->getParam('user', Request::GET, DataTypeUtil::PLAIN);
            $password = RWF::getRequest()->getParam('password', Request::GET, DataTypeUtil::PLAIN);

            $user = UserEditor::getInstance()->getUserByName($userName);
            if($user != null && $user->checkPasswordHash($password)) {

                $rwfUser = $user;
            }
        }

        //alle Elemente durchlaufen
        foreach ($roomElements as $element) {

            if ($element instanceof Switchable && $element->isEnabled() && $element->isVisible() && $element->isUserEntitled($rwfUser)) {

                $data[] = $this->serializeSwitchable($element);
            } elseif ($element instanceof Readable && $element->isEnabled() && $element->isVisible() && $element->isUserEntitled($rwfUser)) {

                $data[] = $this->serializeReadable($element);
            } elseif ($element instanceof Sensor && $element->isVisible()) {

                $data[] = $this->serializeSensor($element);
            } elseif ($element instanceof ViewHelperBox) {

                $tmp = array(
                    'type' => 'Box',
                    'name' => $element->getName()
                );
                $boxElements = $element->listElementsOrdered();
                foreach($boxElements as $boxElement) {

                    if ($boxElement instanceof Switchable && $boxElement->isEnabled() && $boxElement->isVisible() && $boxElement->isUserEntitled($rwfUser)) {

                        $tmp['elements'][] = $this->serializeSwitchable($boxElement);
                    } elseif ($boxElement instanceof Readable && $boxElement->isEnabled() && $boxElement->isVisible() && $boxElement->isUserEntitled($rwfUser)) {

                        $tmp['elements'][] = $this->serializeReadable($boxElement);
                    } elseif ($boxElement instanceof Sensor && $boxElement->isVisible()) {

                        $tmp['elements'][] = $this->serializeSensor($boxElement);
                    }
                }
                $data[] = $tmp;
            }
        }

        $this->data = $data;
    }

    /**
     * Serialisiert ein AbstractSwitchable Element
     *
     * @param Switchable $element
     * @return array
     */
    protected function serializeSwitchable(AbstractSwitchable $element) {

        $data = array(
            'id' => $element->getId(),
            'name' => $element->getName(),
            'icon' => $element->getIcon(),
            'state' => $element->getState()
        );

        if ($element instanceof Activity) {

            $data['type'] = 'Activity';
            $data['buttonText'] = $element->getButtonText();
        } elseif ($element instanceof AvmSocket) {

            $data['type'] = 'AvmSocket';
            $data['buttonText'] = $element->getButtonText();
        } elseif ($element instanceof Countdown) {

            $data['type'] = 'Countdown';
            $data['buttonText'] = $element->getButtonText();
        } elseif ($element instanceof FritzBox) {

            $data['type'] = 'FritzBox';
            $data['function'] = $element->getFunction();
        } elseif ($element instanceof RadioSocket) {

            $data['type'] = 'RadioSocket';
            $data['buttonText'] = $element->getButtonText();
        } elseif ($element instanceof Reboot) {

            $data['type'] = 'Reboot';
        } elseif ($element instanceof RpiGpioOutput) {

            $data['type'] = 'RpiGpioOutput';
            $data['buttonText'] = $element->getButtonText();
        } elseif ($element instanceof Script) {

            $data['type'] = 'Script';
            $data['buttonText'] = $element->getButtonText();
            $data['function'] = ($element->getOnCommand() != '' && $element->getOffCommand() != '' ? 'both' : ($element->getOnCommand() != '' ? 'on' : 'off'));
        } elseif ($element instanceof Shutdown) {

            $data['type'] = 'Shutdown';
        } elseif ($element instanceof WakeOnLan) {

            $data['type'] = 'WakeOnLan';
        }

        return $data;
    }

    /**
     * Serialisiert ein Lesbares Element
     *
     * @param AbstractSwitchable $element
     * @return array
     */
    protected function serializeReadable(Readable $element) {

        return array(
            'type' => 'Input',
            'id' => $element->getId(),
            'name' => $element->getName(),
            'state' => $element->getState()
        );
    }

    /**
     * Serialisiert einen Sensor
     *
     * @param Sensor $element
     * @return array
     */
    protected function serializeSensor(Sensor $element) {

        $data = array(
            'id' => $element->getId(),
            'name' => $element->getName(),
            'icon' => $element->getIcon()
        );

        if ($element instanceof AvmMeasuringSocket) {

            $data['type'] = 'AvmMeasuringSocket';
            $data['temp'] = $element->getDisplayTemperature();
            $data['power'] = $element->getDisplayPower();
            $data['energy'] = $element->getDisplayEnergy();
        } elseif ($element instanceof BMP) {

            $data['type'] = 'BMP';
            $data['temp'] = $element->getDisplayTemperature();
            $data['press'] = $element->getDisplayAirPressure();
            $data['alti'] = $element->getDisplayAltitude();
        } elseif ($element instanceof DHT) {

            $data['type'] = 'DHT';
            $data['temp'] = $element->getDisplayTemperature();
            $data['hum'] = $element->getDisplayHumidity();
        } elseif ($element instanceof DS18x20) {

            $data['type'] = 'DS18x20';
            $data['temp'] = $element->getDisplayTemperature();
        } elseif ($element instanceof Hygrometer) {

            $data['type'] = 'Hygrometer';
            $data['val'] = $element->getDisplayMoisture();
        } elseif ($element instanceof LDR) {

            $data['type'] = 'LDR';
            $data['val'] = $element->getDisplayLightIntensity();
        } elseif ($element instanceof RainSensor) {

            $data['type'] = 'RainSensor';
            $data['val'] = $element->getDisplayMoisture();
        }
        return $data;
    }
}