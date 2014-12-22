<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Runtime\RaspberryPi;
use RWF\Util\String;

/**
 * liest die Statusdaten und sendet sie an die Oberflaeche
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class DataSyncAjax extends AjaxCommand {

    protected $premission = '';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        //Initialisieren
        $data = array();
        $rpi = new RaspberryPi();

        if ($this->request->issetParam('usb', Request::GET)) {

            //USB Daten
            $usb = $rpi->getUsbDevices();
            $html = '';

            foreach ($usb as $device) {

                $html .= '<tr>';
                $html .= '<td><b>' . $device['vendor'] . ' ' . $device['produkt'] . '</b></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . RWF::getLanguage()->get('index.box.usb.count') . ': ' . $device['count'] . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . RWF::getLanguage()->get('index.box.usb.class') . ': ' . $device['interface'] . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . RWF::getLanguage()->get('index.box.usb.current') . ': ' . $device['maxPower'] . '</td>';
                $html .= '</tr>';
                $data = $html;
            }

        } else {

            //Systemdaten
            //Betriebssystem
            $data['osName'] = String::encodeHTML($rpi->getOsName());
            $data['kernel'] = String::encodeHTML($rpi->getKernelVersion());
            $data['firmwareShort'] = String::encodeHTML(String::subString($rpi->getFirmwareVersion(), 0, 45) . ' ...');
            $data['firmware'] = String::encodeHTML($rpi->getFirmwareVersion());
            $split = $rpi->getMemorySplit();
            $data['splitSystem'] = String::encodeHTML($split['system']);
            $data['splitVideo'] = String::encodeHTML($split['video']);
            $data['hostname'] = String::encodeHTML($rpi->getHostname());

            //Systemdaten
            $data['cpuType'] = String::encodeHTML($rpi->getCPUType());
            $overclock = $rpi->getOverclockInfo();
            $data['ocCpu'] = String::formatFloat($overclock['cpu']);
            $data['ocGpu'] = String::formatFloat($overclock['gpu']);
            $data['ocRam'] = String::formatFloat($overclock['ram']);
            $data['ocVoltage'] = String::formatFloat($overclock['voltage'], 3);

            $data['cpuFeatures'] = String::encodeHTML($rpi->getCPUFeatures());
            $data['serial'] = String::encodeHTML($rpi->getRpiSerial());
            $rev = $rpi->getRpiRevision();
            if ($rev == 1) {
                $data['revision'] = 'Beta';
            } elseif ($rev == 2) {
                $data['revision'] = 'Model B Revision 1.0';
            } elseif ($rev == 3) {
                $data['revision'] = 'Model B Revision 1.0 + Fuses mod and D14 removed';
            } elseif ($rev == 4) {
                $data['revision'] = 'Model B Revision 2.0 256MB (Sony)';
            } elseif ($rev == 5) {
                $data['revision'] = 'Model B Revision 2.0 256MB (Qisda)';
            } elseif ($rev == 6) {
                $data['revision'] = 'Model B Revision 2.0 256MB (Egoman)';
            } elseif ($rev == 7) {
                $data['revision'] = 'Model A Revision 2.0 256MB (Egoman)';
            } elseif ($rev == 8) {
                $data['revision'] = 'Model A Revision 2.0 256MB (Sony)';
            } elseif ($rev == 9) {
                $data['revision'] = 'Model A Revision 2.0 256MB (Qisda)';
            } elseif ($rev == 13) {
                $data['revision'] = 'Model B Revision 2.0 512MB (Egoman)';
            } elseif ($rev == 14) {
                $data['revision'] = 'Model B Revision 2.0 512MB (Sony)';
            } elseif ($rev == 15) {
                $data['revision'] = 'Model B Revision 2.0 512MB (Qisda)';
            } elseif ($rev == 16) {
                $data['revision'] = 'Model B+ Revision 2.0 512MB';
            } elseif ($rev == 17) {
                $data['revision'] = 'Compute Module Revision 1.0 512MB (Sony)';
            } elseif ($rev == 18) {
                $data['revision'] = 'Model A+ Revision 1.0 256MB (Sony)';
            } else {
                $data['revision'] = RWF::getLanguage()->get('global.unknown');
            }
            $data['usbCurrent'] = String::numberFormat($rpi->getUsbCurrent());

            //Video Lizensen
            if ($rpi->isSetMPEGCode()) {
                $data['mpeg2'] = RWF::getLanguage()->get('index.box.video.active');
            } else {
                $data['mpeg2'] = RWF::getLanguage()->get('index.box.video.inactive');
            }
            if ($rpi->isSetVC1Code()) {
                $data['vc1'] = RWF::getLanguage()->get('index.box.video.active');
            } else {
                $data['vc1'] = RWF::getLanguage()->get('index.box.video.inactive');
            }
        }

        $this->data = $data;
    }
}