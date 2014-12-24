<?php

namespace PCC\Command\Web;

//Imports
use RWF\Core\RWF;
use RWF\Date\LanguageDateTime;
use RWF\Request\Commands\AjaxCommand;
use RWF\Request\Request;
use RWF\Runtime\RaspberryPi;
use RWF\Util\FileUtil;
use RWF\Util\String;
use RWF\Util\TimeUtil;

/**
 * liest die Statusdaten und sendet sie an die Oberflaeche
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class StateSyncAjax extends AjaxCommand {

    protected $premission = 'pcc.ucp.viewSysState';

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

        if($this->request->issetParam('wireless', Request::GET)) {

            //Wlan Status
            //Daten holen und zur Anzeige aufbereiten
            $devices = $rpi->getWlanState();
            $data['devNum'] = count($devices);
            $data['wireless'] = '';
            foreach($devices as $device) {

                $data['wireless'] .= '
                    <tr>
                        <td>' . String::encodeHTML($device['name']) . '</td>
                        <td>' . String::encodeHTML($device['standard']) . '</td>
                        <td>' . String::encodeHTML(($device['connected'] === true ? $device['ssid'] : RWF::getLanguage()->get('index.box.wlan.notConnected'))) . '</td>
                        <td>' . String::encodeHTML(($device['connected'] === true ? $device['bitRate'] : '0')) . 'MB/s</td>
                        <td>' . String::encodeHTML(($device['connected'] === true ? $device['quality'] : '0/0')) .'  -> '. String::encodeHTML(($device['connected'] === true ? $device['signalLevel'] : '0'))  .'dBm</td>
                    </tr>';
            }
        } else {

            //Systemstatus
            //Systemzeit
            $uptime = TimeUtil::formatTimefromSeconds($rpi->getUptime());
            if (String::length($uptime) > 50) {

                $data['uptimeSort'] = String::subString($uptime, 0, 45) . ' ...';
                $data['uptime'] = $uptime;
            } else {
                $data['uptimeSort'] = $uptime;
                $data['uptime'] = $uptime;
            }

            $date = new LanguageDateTime();
            $date->setTimestamp($rpi->getLastBootTime());
            $data['lastBootTime'] = $date->showDateTime();

            //CPU
            $sysload = $rpi->getSysLoad();
            $data['sysload_0'] = $sysload[0];
            $data['sysload_1'] = $sysload[1];
            $data['sysload_2'] = $sysload[2];

            $data['cpuClock'] = String::formatFloat($rpi->getCpuClock());
            $data['cpuMinClock'] = String::formatFloat($rpi->getCpuMinClock());
            $data['cpuMaxClock'] = String::formatFloat($rpi->getCpuMaxClock());

            $data['coreTemp'] = String::formatFloat($rpi->getCoreTemprature());

            //Speicher
            $memory = $rpi->getMemoryUsage();
            $data['memoryPercent'] = $memory['percent'];
            $data['memoryPercentDisplay'] = String::formatFloat($memory['percent'], 0);
            $data['memoryTotal'] = FileUtil::formatBytesBinary($memory['total']);
            $data['memoryFree'] = FileUtil::formatBytesBinary($memory['free']);
            $data['memoryUsed'] = FileUtil::formatBytesBinary($memory['used']);

            //Swap
            $swap = $rpi->getSwapUsage();
            $data['swapPercent'] = $swap['percent'];
            $data['swapPercentDisplay'] = String::formatFloat($swap['percent'], 0);
            $data['swapTotal'] = FileUtil::formatBytesBinary($swap['total']);
            $data['swapFree'] = FileUtil::formatBytesBinary($swap['free']);
            $data['swapUsed'] = FileUtil::formatBytesBinary($swap['used']);

            //Systemspeicher
            $sysMemory = $rpi->getMemoryInfo();
            $data['sysMemory'] = '';
            foreach ($sysMemory as $index => $mem) {

                if ($index != (count($sysMemory) - 1)) {

                    $data['sysMemory'] .= '
            <tr>
                <td>' . String::encodeHTML($mem['device']) . '</td>
                <td>' . String::encodeHTML($mem['mountpoint']) . '</td>
                <td>
                    <script type="text/javascript">
                        $(function() {
                            $(\'#sysMem_progress_' . $index . '\').progressbar({value: ' . $mem['percent'] . '});
                        });
                    </script>
                    <div class="storage_progress" id="sysMem_progress_' . $index . '"></div>
                </td>
                <td>' . String::encodeHTML($mem['percent']) . '%</td>
                <td>' . FileUtil::formatBytesBinary($mem['total']) . '</td>
                <td>' . FileUtil::formatBytesBinary($mem['used']) . '</td>
                <td>' . FileUtil::formatBytesBinary($mem['free']) . '</td>
            </tr>';
                } else {

                    $data['sysMemoryFoot'] = '
            <tr>
                <td>' . RWF::getLanguage()->get('index.box.memory.total') . '</td>
                <td></td>
                <td>
                    <script type="text/javascript">
                        $(function() {
                            $(\'#sysMem_progress_' . $index . '\').progressbar({value: ' . $mem['percent'] . '});
                        });
                    </script>
                    <div class="storage_progress" id="sysMem_progress_' . $index . '" style="border: solid 1px #ffffff"></div>
                </td>
                <td>' . String::encodeHTML($mem['percent']) . '%</td>
                <td>' . FileUtil::formatBytesBinary($mem['total']) . '</td>
                <td>' . FileUtil::formatBytesBinary($mem['used']) . '</td>
                <td>' . FileUtil::formatBytesBinary($mem['free']) . '</td>
            </tr>';
                }
            }

            //Netzwerk
            $network = $rpi->getNetworkDevices();
            $data['network'] = '';
            foreach ($network as $index => $net) {

                $data['network'] .= '
            <tr>
                <td>' . String::encodeHTML($net['name']) . '</td>
                <td>' . FileUtil::formatBytesBinary($net['in']) . '</td>
                <td>' . FileUtil::formatBytesBinary($net['out']) . '</td>
                <td>' . String::formatFloat($net['errors'], 0) . '/' . String::formatFloat($net['drops'], 0) . '</td>
            </tr>';
            }
        }

        $this->data = $data;
    }
}