<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Runtime\RaspberryPi;
use RWF\Util\StringUtils;
use RWF\Util\FileUtil;

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

    protected $requiredPremission = 'pcc.ucp.viewSysData';

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
            $data['osName'] = StringUtils::encodeHTML($rpi->getOsName());
            $data['kernel'] = StringUtils::encodeHTML($rpi->getKernelVersion());
            $data['firmwareShort'] = StringUtils::encodeHTML(StringUtils::subString($rpi->getFirmwareVersion(), 0, 45) . ' ...');
            $data['firmware'] = StringUtils::encodeHTML($rpi->getFirmwareVersion());
            $data['hostname'] = StringUtils::encodeHTML($rpi->getHostname());

            //RPI Revision
            if($rpi->getRpiRevision() != null) {

                //Board
                $data['boardType'] = 'rpi';

                //Systemdaten
                $data['cpuType'] = StringUtils::encodeHTML($rpi->getCPUType());
                $cpuMemory = $rpi->getCpuMemory();
                $gpuMemory = $rpi->getGPUMemory();
                $data['splitSystem'] = FileUtil::formatBytesBinary($cpuMemory);
                $data['splitVideo'] = FileUtil::formatBytesBinary($gpuMemory);
                $overclock = $rpi->getOverclockInfo();
                $data['ocCpu'] = StringUtils::formatFloat($overclock['cpu']);
                $data['ocGpu'] = StringUtils::formatFloat($overclock['gpu']);
                $data['ocRam'] = StringUtils::formatFloat($overclock['ram']);
                $data['ocVoltage'] = StringUtils::formatFloat($overclock['voltage'], 3);

                $data['cpuFeatures'] = StringUtils::encodeHTML($rpi->getCPUFeatures());
                $data['serial'] = StringUtils::encodeHTML($rpi->getRpiSerial());
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
                    $data['revision'] = 'Model B+ Revision 2.0 512MB (Sony)';
                } elseif ($rev == 17) {
                    $data['revision'] = 'Compute Module Revision 1.0 512MB (Sony)';
                } elseif ($rev == 18) {
                    $data['revision'] = 'Model A+ Revision 1.0 256MB (Sony)';
                } elseif ($rev == 10489921) {
                    $data['revision'] = 'Model 2B 1GB Ram 4x900MHz (Sony)';
                } else {
                    $data['revision'] = RWF::getLanguage()->get('global.unknown');
                }
                $data['usbCurrent'] = StringUtils::numberFormat($rpi->getUsbCurrent());

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
            } else {

                //Board
                $data['boardType'] = 'unknown';
            }
        }

        $this->data = $data;
    }
}