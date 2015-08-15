<?php

namespace RWF\Runtime;

//Imports

/**
 * gibt Daten zum Laufzeitverhalten von PHP zurueck
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Runtime {

    /**
     * Speicherlimit in Bytes
     * 
     * @var Integer
     */
    protected $memoryLimit = 0;

    /**
     * Singleton Instanz
     * 
     * @var \RWF\Runtime\Runtime
     */
    protected static $instance = null;

    protected function __construct() {

        $this->memoryLimit = $this->getBytesFromIniValue(ini_get('memory_limit'));
    }

    /**
     * gibt den benoetigten Arbeistspeicher zurueck
     * 
     * @return Integer
     */
    public function getMemorySize() {

        return memory_get_usage();
    }

    /**
     * gibt die Speicherauslastung in Prozent zurueck
     * 
     * @return Integer
     */
    public function getMemoryUtilization() {

        $size = memory_get_usage();
        $utilization = $size * 100 / $this->memoryLimit;
        $utilization = round($utilization, 2);
        return $utilization;
    }

    /**
     * gibt das Memory Limit der PHP Laufzeitumgebung zurueck
     * 
     * @return Integer
     */
    public function getMemoryLimit() {

        return $this->memoryLimit;
    }

    /**
     * gibt die vergangene Zeit seit dem start der Ausfuehrung in Milisekunden zurueck
     * 
     * @return Float
     */
    public function getMicrotime() {

        return round(strtok(microtime(), ' ') . strtok('') - MICROTIME_NOW, 4);
    }

    /**
     * gibt die PHP Version zurueck
     * 
     * @return String
     */
    public function getPHPVersion() {

        return phpversion();
    }

    /**
     * vergleicht die PHP Versionen
     * 
     * @param  String  $version  Versionstring
     * @param  String  $operator Vergleichsoperation
     * @return Boolean          
     */
    public function comparePHPVersion($version, $operator = '=') {

        if (version_compare(PHP_VERSION, $version, $operator)) {
            return true;
        }
        return false;
    }

    /**
     * gibt die CPU Auslastung der letzten 1, 5 und 15 Minuten zurueck
     * 
     * @param  Integer $time Zeitraum (0 = Array, 1 = letzte Minute, 5 = letzte 5 Minuten, 15 = letzte 15 Minuten)
     * @return Array        
     */
    public function getProcessUtilization($time = 0) {

        if (function_exists('sys_getloadavg')) {
            $utilization = sys_getloadavg();
            if ($time == 1) {
                return $utilization[0];
            } else if ($time == 5) {
                return $utilization[1];
            } else if ($time == 15) {
                return $utilization[2];
            }
            return $utilization;
        }
        return null;
    }

    /**
     * gibt den freien Speicher das Ordners zurueck
     * 
     * @return Integer Bytes
     */
    public function getDiskFreeSpace() {

        if (function_exists('disk_free_space')) {

            return disk_free_space(getcwd());
        }
        return null;
    }

    /**
     * gibt den belegten Speicher das Ordners zurueck
     * 
     * @return Integer Bytes
     */
    public function getDiskTotalSpace() {

        if (function_exists('disk_total_space')) {

            return disk_total_space(getcwd());
        }
        return 0;
    }

    /**
     * gibt das Server betriebsyystem zurueck
     * 
     * @return String Betriebssystem
     */
    public function getRuntimeOS() {

        $os = '';
        $r = php_uname('s') . ' ' . php_uname('r');

        if (preg_match('#win#i', $r)) {
            if (preg_match('#nt\s+6\.1#i', $r)) {
                $os = 'Windows 7';
            } elseif (preg_match('#nt\s+6\.0#i', $r)) {
                $os = 'Windows Vista';
            } elseif (preg_match('#nt\s+5\.1#i', $r)) {
                $os = 'Windows XP';
            } elseif (preg_match('#nt\s+5\.0#i', $r)) {
                $os = 'Windows 2000';
            } elseif (preg_match('#nt\s+4\.0#i', $r)) {
                $os = 'Windows NT';
            } elseif (preg_match('#windows\s+98#i', $r)) {
                $os = 'Windows 98';
            }
        } elseif (preg_match('#mac#i', php_uname('s'))) {
            $os = 'MAC ' . php_uname('r');
        } elseif (preg_match('#linux#i', php_uname('s'))) {
            $os = 'Linux ' . php_uname('r');
        } elseif (preg_match('#unix#i', php_uname('s'))) {
            $os = 'Unix ' . php_uname('r');
        }

        return $os . ' ' . php_uname('m');
    }

    /**
     * gibt den Ini Wert als Bytewert zurueck
     * 
     * @param  String  $val Ini Einstellung
     * @return Integer      
     */
    protected function getBytesFromIniValue($val) {

        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {
        
    }

    /**
     * gibt das Runtime Objekt zurueck
     * 
     * @return \RWF\Runtime\Runtime
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new Runtime();
        }
        return self::$instance;
    }

}
