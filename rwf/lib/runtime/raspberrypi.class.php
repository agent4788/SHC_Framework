<?php

namespace RWF\Runtime;

//Imports

/**
 * liest Raspberry Pi spezifische Daten vom System aus
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RaspberryPi {

    /**
     * Schaltserver auf einem Model A
     * 
     * @var Integer
     */
    const MODEL_A = 1;
    
    /**
     * Schaltserver auf einem Model B
     * 
     * @var Integer
     */
    const MODEL_B = 2;
    
    /**
     * Schaltserver auf einem Model B+
     * 
     * @var Integer
     */
    const MODEL_B_PLUS = 4;
    
    /**
     * CPU Info
     * 
     * @var String 
     */
    protected $cpuInfo = '';

    /**
     * Daten der config.txt
     * 
     * @var Array 
     */
    protected $config = array();

    /**
     * Zwischenspeicher
     * 
     * @var Array 
     */
    protected $cache = array();

    /**
     * initialisiert die Klasse
     */
    public function __construct() {

        $this->cpuInfo = file_get_contents('/proc/cpuinfo');
        $this->config = @parse_ini_file("/boot/config.txt");
    }

    /**
     * gibt den Host Namen des RPI zurueck
     * 
     * @return String
     */
    public function getHostname() {

        if (!isset($this->cache['hostmane'])) {

            $this->cache['hostmane'] = trim(file_get_contents('/proc/sys/kernel/hostname'));
        }
        return $this->cache['hostmane'];
    }

    /**
     * gibt die Host Adresse des RPI zurueck
     * 
     * @return String
     */
    public function getHostAddr() {

        if (!isset($this->cache['hostaddr'])) {

            if (!isset($_SERVER['SERVER_ADDR'])) {

                $this->cache['hostaddr'] = 'unknown';
            }

            if (!$ip = $_SERVER['SERVER_ADDR']) {

                $this->cache['hostaddr'] = gethostbyname($this->getHostname());
            }
        }
        return $this->cache['hostaddr'];
    }

    /**
     * gibt die Kernel Version des RPI OS zurueck
     * 
     * @return String
     */
    public function getKernelVersion() {

        if (!isset($this->cache['kernel'])) {

            $match = array();
            $version = file_get_contents('/proc/version');
            preg_match('#version\s+([^\s]*)#', $version, $match);
            $this->cache['kernel'] = $match[1];
        }
        return $this->cache['kernel'];
    }

    /**
     * gibt die Firmwareversion des RPi zurück
     * 
     * @return String
     */
    public function getFirmwareVersion() {

        $firmware = array();
        exec('uname -a', $firmware);
        return $firmware[0];
    }

    /**
     * gibt die Temperatur des Systemkerns zurueck
     * 
     * @return String
     */
    public function getCoreTemprature() {

        if (!isset($this->cache['coreTemperature'])) {

            $file = @file_get_contents('/sys/class/thermal/thermal_zone0/temp');
            if ($file != false) {

                $this->cache['coreTemperature'] = round(substr(trim($file), 0, 2) . '.' . substr(trim($file), 2), 2);
            } else {

                $this->cache['coreTemperature'] = 0.0;
            }
        }

        return $this->cache['coreTemperature'];
    }

    /**
     * gibt den CPU Takt in MHz zurueck
     * 
     * @return float
     */
    public function getCpuClock() {

        if (!isset($this->cache['cpuClock'])) {

            if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) {

                $file = trim(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq'));
                $this->cache['cpuClock'] = floatval($file) / 1000;
            } else {

                $match = array();
                $file = file_get_contents('/proc/cpuinfo');
                preg_match('#BogoMIPS\s*:\s*(\d*\.\d*)#i', $file, $match);
                $this->cache['cpuClock'] = floatval($match[1]);
            }
        }
        return $this->cache['cpuClock'];
    }

    /**
     * gibt die Minimale CPU Frequenz zurueck
     * 
     * @return Float
     */
    public function getCpuMinClock() {

        if (!isset($this->cache['cpuMinClock'])) {

            $file = @file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_min_freq');
            if ($file != false) {

                $this->cache['cpuMinClock'] = floatval(trim($file)) / 1000;
            } else {

                $this->cache['cpuMinClock'] = 0.0;
            }
        }

        return $this->cache['cpuMinClock'];
    }

    /**
     * gibt die Maximale CPU Frequenz zurueck
     * 
     * @return Float
     */
    public function getCpuMaxClock() {

        if (!isset($this->cache['cpuMaxClock'])) {

            $file = @file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_max_freq');
            if ($file != false) {

                $this->cache['cpuMaxClock'] = floatval(trim($file)) / 1000;
            } else {

                $this->cache['cpuMaxClock'] = 0.0;
            }
        }

        return $this->cache['cpuMaxClock'];
    }

    /**
     * gibt ein Array mit den Overclock Einstellungen zurueck
     * 
     * @return Array
     */
    public function getOverclockInfo() {

        if (!isset($this->cache['overclockInfo'])) {

            $this->cache['overclockInfo'] = array();

            //CPU Frequenz
            if (isset($this->config['arm_freq'])) {

                $this->cache['overclockInfo']['cpu'] = $this->config['arm_freq'];
            } else {

                $this->cache['overclockInfo']['cpu'] = 700;
            }

            //GPU Frequenz
            if (isset($this->config['core_freq'])) {

                $this->cache['overclockInfo']['gpu'] = $this->config['core_freq'];
            } else {

                $this->cache['overclockInfo']['gpu'] = 250;
            }

            //Ram Frequenz
            if (isset($this->config['sdram_freq'])) {

                $this->cache['overclockInfo']['ram'] = $this->config['sdram_freq'];
            } else {

                $this->cache['overclockInfo']['ram'] = 250;
            }

            //Spannung
            if (isset($this->config['over_voltage'])) {

                $this->cache['overclockInfo']['voltage'] = $this->config['over_voltage'];
            } else {

                $this->cache['overclockInfo']['voltage'] = 0;
            }

            //Spannung errechnen
            $this->cache['overclockInfo']['voltage'] = 1.2 + ($this->cache['overclockInfo']['voltage'] * 0.025);
        }

        return $this->cache['overclockInfo'];
    }

    /**
     * gibt den CPU Typ des RPI zurueck
     *
     * @return String Typ
     */
    public function getCPUType() {

        if (!isset($this->cache['cpuType'])) {

            $match = array();
            preg_match('#Hardware\s*:\s*([^\s]+)#i', $this->cpuInfo, $match);
            if (isset($match[1])) {

                $this->cache['cpuType'] = $match[1];
            } else {

                $this->cache['cpuType'] = null;
            }
        }
        return $this->cache['cpuType'];
    }

    /**
     * gibt die CPU Eigenschaften des RPI zurueck
     *
     * @return String Eigenschaften
     */
    public function getCPUFeatures() {

        if (!isset($this->cache['cpuFeatures'])) {

            $match = array();
            preg_match('#Features\s*:\s*(.+)#i', $this->cpuInfo, $match);

            if (isset($match[1])) {

                $this->cache['cpuFeatures'] = $match[1];
            } else {
                $this->cache['cpuFeatures'] = null;
            }
        }
        return $this->cache['cpuFeatures'];
    }

    /**
     * gibt die Seriennummer des RPI zurueck
     * 
     * @return String Serien Nummer
     */
    public function getRpiSerial() {

        if (!isset($this->cache['rpiSerial'])) {

            $match = array();
            preg_match('#Serial\s*:\s*([\da-f]+)#i', $this->cpuInfo, $match);

            if (isset($match[1])) {

                $this->cache['rpiSerial'] = $match[1];
            } else {
                $this->cache['rpiSerial'] = null;
            }
        }

        return $this->cache['rpiSerial'];
    }

    /**
     * gibt die Revisionsnummer des RPI zurueck
     *
     * @return String Serien Nummer
     */
    public function getRpiRevision() {

        //'0002' => 'Model B Revision 1.0',
        //'0003' => 'Model B Revision 1.0 + Fuses mod and D14 removed',
        //'0004' => 'Model B Revision 2.0 256MB', (Sony)
        //'0005' => 'Model B Revision 2.0 256MB', (Qisda)
        //'0006' => 'Model B Revision 2.0 256MB', (Egoman)
        //'0007' => 'Model A Revision 2.0 256MB', (Egoman)
        //'0008' => 'Model A Revision 2.0 256MB', (Sony)
        //'0009' => 'Model A Revision 2.0 256MB', (Qisda)
        //'000d' => 'Model B Revision 2.0 512MB', (Egoman)
        //'000e' => 'Model B Revision 2.0 512MB', (Sony)
        //'000f' => 'Model B Revision 2.0 512MB', (Qisda)
        //'0010' => 'Model B+ Revision 2.0 512MB', (Sony)
        if (!isset($this->cache['rpiRevision'])) {

            $match = array();
            preg_match('#\nRevision\s*:\s*([\da-f]+)#i', $this->cpuInfo, $match);

            if (isset($match[1])) {

                $this->cache['rpiRevision'] = hexdec($match[1]);
            } else {
                $this->cache['rpiRevision'] = null;
            }
        }
        return $this->cache['rpiRevision'];
    }
    
    public function getModel() {
        
        $rev = $this->getRpiRevision();
        if(($rev >= 1 && $rev <= 6) || ($rev >= 10 && $rev <= 15)) {
            
            return self::MODEL_B;
        } elseif($rev >= 7 && $rev <= 9) {
            
            return self::MODEL_A;
        }
        return self::MODEL_B_PLUS;
    }

    /**
     * gibt ein Array mit der AUfteilung von System und Videospeicher zurueck
     * 
     * @return Array
     */
    public function getMemorySplit() {

        if (!isset($this->cache['memorySplit'])) {

            //Revision
            $rev = $this->getRpiRevision();

            //split in der config
            $gpuMem = array();
            if (isset($this->config['gpu_mem'])) {
                $gpuMem[0] = $this->config['gpu_mem'];
            }
            if (isset($this->config['gpu_mem_256'])) {
                $gpuMem[1] = $this->config['gpu_mem_256'];
            }
            if (isset($this->config['gpu_mem_512'])) {
                $gpuMem[2] = $this->config['gpu_mem_512'];
            }

            //512 MB RPi
            if ($rev == 13 || $rev == 14 || $rev == 15 || $rev == 16) {

                if (isset($gpuMem[2])) {

                    $total = $gpuMem[2];
                } elseif (isset($gpuMem[0])) {

                    $total = $gpuMem[0];
                } else {

                    $mem = $this->getMemoryUsage();
                    $total = round($mem ['total'] / 1024 / 1024, 0);
                    if ($total <= 256) {

                        $total = 256;
                    } elseif ($total > 256 && $total <= 384) {

                        $total = 128;
                    } elseif ($total > 384 && $total <= 448) {

                        $total = 64;
                    } elseif ($total > 448 && $total <= 480) {

                        $total = 32;
                    } else {

                        $total = 16;
                    }
                }

                if ($total == 16) {

                    $this->cache['memorySplit'] = array('system' => '496 MiB', 'video' => '16 MiB');
                } elseif ($total == 32) {

                    $this->cache['memorySplit'] = array('system' => '480 MiB', 'video' => '32 MiB');
                } elseif ($total == 64) {

                    $this->cache['memorySplit'] = array('system' => '448 MiB', 'video' => '64 MiB');
                } elseif ($total == 128) {

                    $this->cache['memorySplit'] = array('system' => '384 MiB', 'video' => '128 MiB');
                } else {

                    $this->cache['memorySplit'] = array('system' => '256 MiB', 'video' => '256 MiB');
                }
            } else {

                //256MB RPi
                if (isset($gpuMem[1])) {

                    $total = $gpuMem[1];
                } elseif (isset($gpuMem[0])) {

                    $total = $gpuMem[0];
                } else {

                    $mem = $this->getMemoryUsage();
                    $total = round($mem ['total'] / 1024 / 1024, 0);

                    if ($total <= 128) {

                        $total = 128;
                    } elseif ($total > 128 && $total <= 192) {

                        $total = 64;
                    } elseif ($total > 192 && $total <= 224) {

                        $total = 32;
                    } else {

                        $total = 16;
                    }
                }

                if ($total == 16) {

                    $this->cache['memorySplit'] = array('system' => '240 MiB', 'video' => '16 MiB');
                } elseif ($total == 32) {

                    $this->cache['memorySplit'] = array('system' => '224 MiB', 'video' => '32 MiB');
                } elseif ($total == 64) {

                    $this->cache['memorySplit'] = array('system' => '192 MiB', 'video' => '64 MiB');
                } else {

                    $this->cache['memorySplit'] = array('system' => '128 MiB', 'video' => '128 MiB');
                }
            }
        }
        return $this->cache['memorySplit'];
    }

    /**
     * gibt die Systemauslastung zurueck
     * 
     * @return Array Auslastung der letzten 1, 5 und 15 min
     */
    public function getSysLoad() {

        if (!isset($this->cache['sysLoad'])) {

            $this->cache['sysLoad'] = sys_getloadavg();
        }
        return $this->cache['sysLoad'];
    }

    /**
     * gibt ein Array mit der Ram Auslastung zurueck
     * 
     * @return Array
     */
    public function getMemoryUsage() {

        if (!isset($this->cache['memoryUsage'])) {

            $matches = array();
            $this->cache['memoryUsage'] = array();
            $meminfo = file_get_contents('/proc/meminfo');
            $meminfo = preg_split('#\n#', $meminfo, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($meminfo as $row) {
                if (preg_match('#^MemTotal:\s+(.*)\s*kB#i', $row, $matches)) {

                    $total = $matches[1] * 1024;
                } elseif (preg_match('#^MemFree:\s+(.*)\s*kB#i', $row, $matches)) {

                    $free = $matches[1] * 1024;
                } elseif (preg_match('#^Cached:\s+(.*)\s*kB#i', $row, $matches)) {

                    $cached = $matches[1] * 1024;
                } elseif (preg_match('#^Buffers:\s+(.*)\s*kB#i', $row, $matches)) {

                    $buffers = $matches[1] * 1024;
                }
            }
            $used = $total - $free;
            $usage = round(($used - $buffers - $cached) / $total * 100);
            $this->cache['memoryUsage'] = array('percent' => $usage, 'total' => (int) $total, 'free' => ($free + $buffers + $cached), 'used' => ($used - $buffers - $cached));
        }
        return $this->cache['memoryUsage'];
    }

    /**
     * gibt ein Array mit der Swap Auslastung zurueck
     *
     * @return Array
     */
    public function getSwapUsage() {

        if (!isset($this->cache['swapUsage'])) {

            $swapsFile = file_get_contents('/proc/swaps');
            $swaps = preg_split("#\n#", $swapsFile, -1, PREG_SPLIT_NO_EMPTY);
            if (isset($swaps[1])) {
                $swap = preg_split('#\s+#', $swaps[1], -1, PREG_SPLIT_NO_EMPTY);

                $this->cache['swapUsage'] = array('percent' => round($swap[3] / $swap[2] * 100) * 1024, 'total' => (int) $swap[2] * 1024, 'free' => (int) ($swap[2] - $swap[3]) * 1024, 'used' => (int) $swap[3] * 1024);
            } else {

                $this->cache['swapUsage'] = array('percent' => 0, 'total' => 0, 'free' => 0, 'used' => 0);
            }
        }
        return $this->cache['swapUsage'];
    }

    /**
     * gibt die Zeit seit dem Start des Systems zurueck
     * 
     * @return Integer
     */
    public function getUptime() {

        if (!isset($this->cache['uptime'])) {

            $file = file_get_contents('/proc/uptime');
            $time = preg_split('#\s+#', trim($file));

            $this->cache['uptime'] = round($time[0], 0);
        }
        return $this->cache['uptime'];
    }

    /**
     * gibt den Zeitstempel des Letzten Boot vorgangs zurueck
     * 
     * @return Integer
     */
    public function getLastBootTime() {

        if (!isset($this->cache['lastBoot'])) {

            $this->cache['lastBoot'] = TIME_NOW - $this->getUptime();
        }
        return $this->cache['lastBoot'];
    }

    /**
     * gibt ein Array mit den Angeschlossenen Speichern zurueck
     * 
     * @return Array
     */
    public function getMemoryInfo() {

        if (!isset($this->cache['memoryInfo'])) {

            $data = '';
            exec('df -lT | grep -vE "devtmpfs|udev|none|rootfs|Filesystem|Dateisystem"', $data);

            $devices = array();
            $totalSize = 0;
            $usedSize = 0;
            foreach ($data as $row) {

                list($device, $type, $blocks, $use, $available, $used, $mountpoint) = preg_split('#[^\dA-Z/]+#i', $row);

                $totalSize += $blocks * 1024;
                $usedSize += $use * 1024;

                $devices[] = array(
                    'device' => $device,
                    'type' => $type,
                    'total' => $blocks * 1024,
                    'used' => $use * 1024,
                    'free' => $available * 1024,
                    'percent' => round(($use * 100 / $blocks), 0),
                    'mountpoint' => $mountpoint
                );
            }

            $devices[] = array('total' => $totalSize, 'used' => $usedSize, 'free' => $totalSize - $usedSize, 'percent' => round(($usedSize * 100 / $totalSize), 0));
            $this->cache['memoryInfo'] = $devices;
        }
        return $this->cache['memoryInfo'];
    }

    /**
     * gibt ein Array mit den Netzwerkschnittstellen zurueck
     * 
     * @return Array
     */
    public function getNetworkDevices() {

        if (!isset($this->cache['networkDevices'])) {

            $dev = file_get_contents('/proc/net/dev');
            $devices = preg_split('#\n#', $dev, -1, PREG_SPLIT_NO_EMPTY);
            unset($devices[0], $devices[1]);

            $netDev = array();
            foreach ($devices as $device) {

                list($dev_name, $stats) = preg_split('#:#', $device);
                $stats = preg_split('#\s+#', trim($stats));
                $netDev[] = array(
                    'name' => trim($dev_name),
                    'in' => $stats[0],
                    'out' => $stats[8],
                    'errors' => $stats[2] + $stats[10],
                    'drops' => $stats[3] + $stats[11]
                );
            }

            $this->cache['networkDevices'] = $netDev;
        }
        return $this->cache['networkDevices'];
    }

    /**
     * gibt zu allen Wlan Geräten den status aus
     * 
     * @return Array
     */
    public function getWlanState() {

        if (!isset($this->cache['wirelessDevices'])) {

            $this->cache['wirelessDevices'] = array();

            $devices = $this->getNetworkDevices();
            $wlanDevices = array();
            foreach ($devices as $device) {

                //pruefen ob es sich um ein WLan Geraet handelt
                if (preg_match('#wlan\d+#i', $device['name'])) {

                    $dev = array();
                    $dev['name'] = $device['name'];
                    $dev['connected'] = false;

                    //Daten abrufen
                    $data = '';
                    exec('iwconfig ' . $device['name'], $data);

                    //Wlan Standard
                    $matches = array();
                    preg_match('#IEEE (802\.11[^\s]+)#i', $data[0], $matches);
                    $dev['standard'] = $matches[1];

                    //SSID
                    $matches = array();
                    preg_match('#ESSID:"(.*?)"#i', $data[0], $matches);
                    $dev['ssid'] = (isset($matches[1]) ? $matches[1] : '');

                    //Wlan mit Netzwerk verbunden
                    if ($dev['ssid'] != '') {

                        $dev['connected'] = true;
                    }

                    //nur wenn mit Wlan Netz verbunden
                    if ($dev['connected'] === true) {

                        //Frequenz
                        $matches = array();
                        preg_match('#Frequency:(\d+\.\d+)#i', $data[1], $matches);
                        $dev['frequency'] = $matches[1];

                        //Access Point
                        $matches = array();
                        preg_match('#Access\s+Point:\s+([0-9a-f:]{17})#i', $data[1], $matches);
                        $dev['accessPoint'] = $matches[1];

                        //Uebetragungsrate
                        $matches = array();
                        preg_match('#Bit\s+Rate\=(\d+)\s+Mb/s#i', $data[2], $matches);
                        $dev['bitRate'] = $matches[1];

                        //Verbindungsqualitaet
                        $matches = array();
                        preg_match('#Link\s+Quality\=(\d+\/\d+)#i', $data[5], $matches);
                        $dev['quality'] = $matches[1];

                        //Signalstaerke
                        $matches = array();
                        preg_match('#Signal\s+level\=(-?\d+)\s+dBm#i', $data[5], $matches);
                        $dev['signalLevel'] = $matches[1];
                    }

                    $this->cache['wirelessDevices'][$dev['name']] = $dev;
                }
            }
        }
        return $this->cache['wirelessDevices'];
    }

    /**
     * gibt den maximalen Strom zurueck den die USB Schnittstellen liefern kann
     * 
     * @return Integer
     */
    public function getUsbCurrent() {

        if (!isset($this->cache['usbCurrent'])) {

            $revision = $this->getRpiRevision();
            if (($revision >= 10) && ((isset($this->config['safe_mode_gpio']) && $this->config['safe_mode_gpio'] == 4) || (isset($this->config['max_usb_current']) && $this->config['max_usb_current'] == 1))) {

                //Model B+ mit freigeschaltener Option liefert max. 1200mA
                $this->cache['usbCurrent'] = 1200;
            } elseif ($revision >= 10) {

                //Model B+ ohne freigeschaltener Option liefert max. 600mA
                $this->cache['usbCurrent'] = 600;
            } else {

                //Model A und B liefert max. 500mA
                $this->cache['usbCurrent'] = 500;
            }
        }
        return $this->cache['usbCurrent'];
    }

    /**
     * gibt ein Array mit allen angeschlossenen USB Geraeten aus
     * 
     * @return Array
     */
    public function getUsbDevices() {

        if (!isset($this->cache['usbDevices'])) {

            $data = '';
            $devices = array();
            $outputDevices = array();

            exec('lsusb -v', $data);

            $i = -1;
            foreach ($data as $row) {

                //Liste Trennen in die einzelnen Geraete
                if ($row == '') {

                    $i++;
                } else {

                    $devices[$i][] = $row;
                }
            }

            //Alle Geraete einzeln durchlaufen und die entsprechenden Daten ermitteln
            foreach ($devices as $devId => $device) {

                foreach ($device as $deviceData) {

                    $match = array();
                    if (preg_match('#\s+idVendor(.*)#i', $deviceData, $match)) {

                        //VendorID
                        $outputDevices[$devId]['idVendor'] = String::trim($match[1]);
                    } elseif (preg_match('#\s+idProduct(.*)#i', $deviceData, $match)) {

                        //Produkt Id
                        $outputDevices[$devId]['idProduct'] = String::trim($match[1]);
                    } elseif (preg_match('#\s+MaxPower(.*)#i', $deviceData, $match)) {

                        //Maximal Strom
                        $outputDevices[$devId]['MaxPower'] = String::trim($match[1]);
                    } elseif (preg_match('#      bInterfaceClass(.*)#i', $deviceData, $match)) {

                        //Schnittstellen Klasse
                        $outputDevices[$devId]['bInterfaceClass'] = String::trim($match[1]);
                    }
                }

                $outputDevices[$devId]['ident'] = $outputDevices[$devId]['idVendor'] . $outputDevices[$devId]['idProduct'];
            }

            //Daten Nachbehandeln und zusammenfassen
            $knownDevices = array();
            $countDevices = array();
            foreach ($outputDevices as $devId => $device) {

                if (!in_array($device['ident'], $knownDevices)) {

                    $knownDevices[] = $device['ident'];
                    $countDevices[] = array(
                        'ident' => $device['ident'],
                        'count' => 1
                    );
                } else {

                    foreach ($countDevices as $id => $count) {

                        if ($count['ident'] == $device['ident']) {

                            $countDevices[$id]['count'] = $count['count'] + 1;
                        }
                    }
                }
            }

            $cleanDevices = array();
            foreach ($countDevices as $device) {

                foreach ($outputDevices as $dev) {

                    if ($dev['ident'] == $device['ident']) {

                        $cleanDevices[] = array(
                            'ident' => $dev['ident'],
                            'count' => $device['count'],
                            'vendor' => String::trim(preg_replace('#(0x[\da-f]{4})#i', '', $dev['idVendor'])),
                            'produkt' => String::trim(preg_replace('#(0x[\da-f]{4})#i', '', $dev['idProduct'])),
                            'maxPower' => $dev['MaxPower'],
                            'interface' => String::trim(preg_replace('#(\d{1,2})#', '', $dev['bInterfaceClass'])),
                        );
                        break;
                    }
                }
            }

            $this->cache['usbDevices'] = $cleanDevices;
        }
        return $this->cache['usbDevices'];
    }

    /**
     * gibt den Namen des Betriebsystems zurueck
     * 
     * @return String
     */
    public function getOsName() {

        if (!isset($this->cache['osName'])) {

            $files = FileUtil::listDirFiles('/etc');

            foreach ($files as $file) {

                if (isset($file['name']) && preg_match('#[a-z]+-release#', $file['name'])) {

                    $match = array();
                    $content = file_get_contents('/etc/' . $file['name']);
                    preg_match('#pretty_name="(.+)"#i', $content, $match);
                    if (isset($match[1])) {

                        $this->cache['osName'] = $match[1];
                    }
                }
            }
            if (!isset($this->cache['osName'])) {

                $this->cache['osName'] = 'unkonwn';
            }
        }
        return $this->cache['osName'];
    }

    /**
     * gibt an ob der VC1 Code gesetzt ist
     * 
     * @return Boolean
     */
    public function isSetVC1Code() {

        if (!isset($this->cache['vc1'])) {

            if (isset($this->config['decode_WVC1']) && preg_match('#0x[0-9a-z]+#i', $this->config['decode_WVC1'])) {

                $this->cache['vc1'] = true;
            } else {

                $this->cache['vc1'] = false;
            }
        }
        return $this->cache['vc1'];
    }

    /**
     * gibt an ob der MPEG Code gesetzt ist
     *
     * @return Boolean
     */
    public function isSetMPEGCode() {

        if (!isset($this->cache['mpeg2'])) {

            $config = @file_get_contents('/boot/config.txt');
            if (isset($this->config['decode_MPG2']) && preg_match('#0x[0-9a-z]+#i', $this->config['decode_MPG2'])) {

                $this->cache['mpeg2'] = true;
            } else {

                $this->cache['mpeg2'] = false;
            }
        }
        return $this->cache['mpeg2'];
    }

}
