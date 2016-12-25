<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use SHC\Core\SHC;
use SHC\Room\Room;
use SHC\Room\RoomEditor;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\SwitchServer\SwitchServerEditor;
use SHC\Util\RadioSocketsUtil;

/**
 * Listet die schaltbaren Elemente
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AssignmentPage extends PageCommand {

    protected $template = 'assignment.html';

    protected $requiredPremission = 'shc.acp.switchableManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'switchablemanagement', 'acpindex');

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
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listswitchables');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Typ zuruecksetzen falls vorhanden
        if(RWF::getSession()->issetVar('type')) {

            RWF::getSession()->remove('type');
        }

        //Daten vorbereiten
        $radioSockets = array();
        $gpios = array();
        $switchables = SwitchableEditor::getInstance()->listElements(SwitchableEditor::SORT_NOTHING);
        foreach($switchables as $switchable) {

            if($switchable instanceof RadioSocket) {

                //Funksteckdose
                $sysCode = $switchable->getSystemCode();

                //Systemcode in Dezimalform wandeln
                if(RadioSocketsUtil::isBinary($sysCode)) {

                    $sysCode = RadioSocketsUtil::convertBinaryToDec($sysCode);
                }

                $rooms = array();
                foreach($switchable->getRooms() as $roomId) {

                    $room = RoomEditor::getInstance()->getRoomById($roomId);
                    if($room instanceof Room) {

                        $rooms[] = $room->getName();
                    }
                }

                $radioSockets[$sysCode][$switchable->getDeviceCode()] = array(
                    'sysCodeDec' => $sysCode,
                    'sysCodeBin' => RadioSocketsUtil::convertDecToBinary($sysCode),
                    'devCode' => $switchable->getDeviceCode(),
                    'name' => $switchable->getName(),
                    'protocol' => $switchable->getProtocol(),
                    'rooms' => $rooms
                );
            } elseif($switchable instanceof RpiGpioInput || $switchable instanceof RpiGpioOutput) {

                $rooms = array();
                foreach($switchable->getRooms() as $roomId) {

                    $room = RoomEditor::getInstance()->getRoomById($roomId);

                    if($room instanceof Room) {

                        $rooms[] = $room->getName();
                    }
                }

                //GPIO
                $switchServer = SwitchServerEditor::getInstance()->getSwitchServerById($switchable->getSwitchServer());
                $gpios[$switchable->getSwitchServer()][$switchable->getPinNumber()] = array(
                    'switchServer' => $switchServer->getName(),
                    'model' => $switchServer->getModel(),
                    'type' => ($switchable instanceof RpiGpioInput ? 'Input' : 'Output'),
                    'pin' => $switchable->getPinNumber(),
                    'name' => $switchable->getName(),
                    'rooms' => $rooms
                );
            }
        }

        //Sortieren
        foreach($radioSockets as $index => $group) {

            ksort($radioSockets[$index], SORT_NATURAL);
        }
        foreach($gpios as $index => $group) {

            ksort($gpios[$index], SORT_NATURAL);
        }

        //Template vorbereiten und Anzeigen
        $tpl->assign('radioSockets', $radioSockets);
        $tpl->assign('gpios', $gpios);
    }

}