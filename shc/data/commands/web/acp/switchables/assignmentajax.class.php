<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\AjaxCommand;
use RWF\Core\RWF;
use SHC\Switchable\Readables\RpiGpioInput;
use SHC\Switchable\SwitchableEditor;
use SHC\Switchable\Switchables\RadioSocket;
use SHC\Switchable\Switchables\RpiGpioOutput;
use SHC\SwitchServer\SwitchServerEditor;
use SHC\Util\RadioSocketsUtil;

/**
 * Zeigt eine Liste mit den Belegten System/Unit Codes und ID's an
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AssignmentAjax extends AjaxCommand {

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

                $radioSockets[$sysCode][$switchable->getDeviceCode()] = array(
                    'sysCodeDec' => $sysCode,
                    'sysCodeBin' => RadioSocketsUtil::convertDecToBinary($sysCode),
                    'devCode' => $switchable->getDeviceCode(),
                    'name' => $switchable->getName(),
                    'room' => $switchable->getRoom()->getName()
                );
            } elseif($switchable instanceof RpiGpioInput || $switchable instanceof RpiGpioOutput) {

                //GPIO
                $gpios[$switchable->getSwitchServer()][$switchable->getPinNumber()] = array(
                    'switchServer' => SwitchServerEditor::getInstance()->getSwitchServerById($switchable->getSwitchServer())->getName(),
                    'type' => ($switchable instanceof RpiGpioInput ? 'Input' : 'Output'),
                    'pin' => $switchable->getPinNumber(),
                    'name' => $switchable->getName(),
                    'room' => $switchable->getRoom()->getName()
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
        $tpl = RWF::getTemplate();
        $tpl->assign('radioSockets', $radioSockets);
        $tpl->assign('gpios', $gpios);
        $this->data = $tpl->fetchString('assignment.html');
    }
}