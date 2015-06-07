<?php

namespace PCC\Command\Smartphone;

//Imports
use PCC\Core\PCC;
use RWF\Core\RWF;
use RWF\Date\DateTime;
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

    protected $requiredPremission = 'pcc.ucp.viewSysState';

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
            $data['wireless'] = '';
            $headline = '<li data-role="list-divider" role="heading">'. PCC::getLanguage()->get('index.box.wlan.title') .'</li>';
            foreach($devices as $device) {

                $data['wireless'] .= $headline;
                $data['wireless'] .= '
                    <li>
                        <h1>' . String::encodeHTML($device['name']) . '</h1>
                        <p>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.wlan.standard') .':</span> ' . String::encodeHTML($device['standard']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.wlan.ssid') .':</span> ' . String::encodeHTML(($device['connected'] === true ? $device['ssid'] : RWF::getLanguage()->get('index.box.wlan.notConnected'))) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.wlan.speed') .':</span> ' . String::encodeHTML(($device['connected'] === true ? $device['bitRate'] : '0')) . 'MB/s
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.wlan.quality') .':</span> ' . String::encodeHTML(($device['connected'] === true ? $device['quality'] : '0/0')) .'  -> '. String::encodeHTML(($device['connected'] === true ? $device['signalLevel'] : '0'))  .'dBm
                        </p>
                    </li>
                    ';
                $headline = '';
            }
        } else {

            //Systemstatus
            //Systemzeit
            $uptime = TimeUtil::formatTimefromSeconds($rpi->getUptime());
            $data['uptime'] = $uptime;

            $date = new LanguageDateTime();
            $date->setTimestamp($rpi->getLastBootTime());
            $data['lastBootTime'] = $date->showDateTime();
            $data['clock'] = DateTime::now()->format('d.m.Y H:i:s');

            //CPU
            $sysload = $rpi->getSysLoad();
            $data['sysload_0'] = $sysload[0];
            $data['sysload_1'] = $sysload[1];
            $data['sysload_2'] = $sysload[2];

            $html = '';
            $brake = '';
            foreach($rpi->getCpuClock() as $cpuId => $value) {

                if($cpuId == 0) {

                    $data['cpuClockCpu0'] = ($value > 1000 ? String::formatFloat($value / 1000) . ' GHz' : String::formatFloat($value) .' MHz');
                }
                $html .= $brake . '<span style="font-weight: bold;">cpu'. $cpuId .'</span>: '. ($value > 1000 ? String::formatFloat($value / 1000) . ' GHz' : String::formatFloat($value) .' MHz');
                $brake = '<br/>';
            }
            $data['cpuClock'] = $html;
            $html = '';
            $brake = '';
            foreach($rpi->getCpuMinClock() as $cpuId => $value) {

                $html .= $brake . '<span style="font-weight: bold;">cpu'. $cpuId .'</span>: '. ($value > 1000 ? String::formatFloat($value / 1000) . ' GHz' : String::formatFloat($value) .' MHz');
                $brake = '<br/>';
            }
            $data['cpuMinClock'] = $html;
            $html = '';
            $brake = '';
            foreach($rpi->getCpuMaxClock() as $cpuId => $value) {

                $html .= $brake . '<span style="font-weight: bold;">cpu'. $cpuId .'</span>: '. ($value > 1000 ? String::formatFloat($value / 1000) . ' GHz' : String::formatFloat($value) .' MHz');
                $brake = '<br/>';
            }
            $data['cpuMaxClock'] = $html;

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
            $data['sysMemory'] = '<li data-role="list-divider" role="heading">'. PCC::getLanguage()->get('index.box.memory.title') .'</li>';
            foreach ($sysMemory as $index => $mem) {

                if ($index != (count($sysMemory) - 1)) {

                    $data['sysMemory'] .= '
                    <li>
                        <h1>' . String::encodeHTML($mem['device']) . '</h1>
                        <p>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.mountpoint') .':</span> ' . String::encodeHTML($mem['mountpoint']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.usage') .':</span> ' . String::formatFloat($mem['percent'], 0) . '%
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.total') .':</span> ' . FileUtil::formatBytesBinary($mem['total']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.used') .':</span> ' . FileUtil::formatBytesBinary($mem['used']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.free') .':</span> ' . FileUtil::formatBytesBinary($mem['free']) . '
                        </p>
                    </li>
                    ';
                } else {

                    $data['sysMemory'] .= '
                    <li data-role="list-divider" role="heading">' . RWF::getLanguage()->get('index.box.memory.total') . '</li>
                    <li>
                        <p>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.usage') .':</span> ' . String::formatFloat($mem['percent'], 0) . '%
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.total') .':</span> ' . FileUtil::formatBytesBinary($mem['total']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.used') .':</span> ' . FileUtil::formatBytesBinary($mem['used']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.memory.free') .':</span> ' . FileUtil::formatBytesBinary($mem['free']) . '
                        </p>
                    </li>
                    ';
                }
            }

            //Netzwerk
            $network = $rpi->getNetworkDevices();
            $data['network'] = '<li data-role="list-divider" role="heading">' . RWF::getLanguage()->get('index.box.network.title') . '</li>';
            foreach ($network as $index => $net) {

                $data['network'] .= '
                    <li>
                        <h1>' . String::encodeHTML($net['name']) . '</h1>
                        <p>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.network.recived') .':</span> ' . FileUtil::formatBytesBinary($net['in']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.network.transmitted') .':</span> ' . FileUtil::formatBytesBinary($net['out']) . '
                            <br/>
                            <span style="font-weight: bold;">'. PCC::getLanguage()->get('index.box.network.errors') .':</span> ' . String::formatFloat($net['errors'], 0) . '/' . String::formatFloat($net['drops'], 0) . '
                        </p>
                    </li>
                    ';
            }
        }

        $this->data = $data;
    }
}