<?php

namespace SHC\Backup;

//Imports
use RWF\Date\DateTime;
use RWF\Util\FileUtil;
use RWF\XML\XmlFileManager;
use SHC\Core\SHC;

/**
 * Backup Verwaltung
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class BackupEditor {

    /**
     * nach Namen sortieren
     *
     * @var String
     */
    const SORT_BY_NAME = 'name';

    /**
     * nicht sortieren
     *
     * @var String
     */
    const SORT_NOTHING = 'unsorted';

    /**
     * Ordner in dem die Backups liegen
     *
     * @var String
     */
    protected $backupPath = '';

    /**
     * Liste mit allen Backups
     *
     * @var Array
     */
    protected $backups = array();

    /**
     * Singleton Instanz
     *
     * @var \SHC\Backup\BackupEditor
     */
    protected static $instance = null;

    protected function __construct(){}

    /**
     * laedt die Daten aus dem Dateisystem
     */
    public function loadData() {

        $files = FileUtil::listDirectoryFiles($this->backupPath, false, true, true);
        foreach($files as $file) {

            $hash = md5($file['name']);
            $this->backups[$hash] = new Backup($hash, $this->backupPath, $file['name']);

        }
    }

    /**
     * setzt den Pfad in dem sich die Backups befinden
     *
     * @param  String $path Pfad
     * @return \SHC\Backup\BackupEditor
     */
    public function setPath($path){

        $this->backupPath = $path;
        $this->loadData();
        return $this;
    }

    /**
     * gibt den Pfad in dem sich die Backups befinden zurueck
     *
     * @return String
     */
    public function getPath() {

        return $this->backupPath;
    }

    /**
     * gibt eine Backup aufgrund eines MD5 Haches des Dateinamens zurueck
     *
     * @param  String $hash MD5 Hash
     * @return \SHC\Backup\Backup
     */
    public function getBackupByMD5Hash($hash) {

        if(isset($this->backups[$hash])) {

            return $this->backups[$hash];
        }
        return null;
    }

    /**
     * gibt eine Liste mit allen vorhandenen Backups zurueck
     *
     * @param  String $orderBy Art der Sortierung (
     *      name => nach Namen sortieren,
     *      unsorted => unsortiert
     *  )
     * @return Array
     */
    public function listBackups($orderBy = 'name') {

        if ($orderBy == 'name') {

            //nach Namen sortieren
            $backups = $this->backups;

            //Sortierfunktion
            $orderFunction = function($a, $b) {

                if ($a->getFileName() == $b->getFileName()) {

                    return 0;
                }

                if ($a->getFileName() < $b->getFileName()) {

                    return -1;
                }
                return 1;
            };
            usort($backups, $orderFunction);
            return $backups;
        }
        return $this->backups;
    }

    /**
     * erstellt eun vollstaendiges Backup der Anwendung
     *
     * @param  Boolean $ignoreHiddenFiles versteckte Dateien ignorieren
     * @return Boolean
     */
    public function makeBackup($ignoreHiddenFiles = false) {

        return $this->makeFileBackup($ignoreHiddenFiles);
    }

    /**
     * loescht ein Backup
     *
     * @param  String  $hash MD5 Hash
     * @return Boolean
     */
    public function removeBackup($hash) {

        if(isset($this->backups[$hash])) {

            /* @var $backup \SHC\Backup\Backup  */
            $backup = $this->backups[$hash];
            if(@unlink($backup->getPath() . $backup->getFileName())) {

                return true;
            }
        }
        return false;
    }

    /**
     * Dateien sichern
     *
     * @param  Boolean $ignoreHiddenFiles versteckte Dateien ignorieren
     * @return Boolean
     */
    protected function makeFileBackup($ignoreHiddenFiles = false) {

        $filename = $this->backupPath . 'shc__' . DateTime::now()->format('Y_m_d__H_i') .'__'. md5(DateTime::now()->getTimestamp()) . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($filename, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) === true) {

            //Datanbank verbindung aufbauen
            $db = SHC::getDatabase();

            //Daten aus Redis in eine JSON Datei schreiben
            $data = array();

            //AutoIncrements
            $data['autoIncrement:conditions'] = $db->get('autoIncrement:conditions');
            $data['autoIncrement:events'] = $db->get('autoIncrement:events');
            $data['autoIncrement:roomView'] = $db->get('autoIncrement:roomView');
            $data['autoIncrement:roomView_order'] = $db->get('autoIncrement:roomView_order');
            $data['autoIncrement:rooms'] = $db->get('autoIncrement:rooms');
            $data['autoIncrement:switchServers'] = $db->get('autoIncrement:switchServers');
            $data['autoIncrement:switchables'] = $db->get('autoIncrement:switchables');
            $data['autoIncrement:switchpoints'] = $db->get('autoIncrement:switchpoints');
            $data['autoIncrement:usersrathome'] = $db->get('autoIncrement:usersrathome');

            //Bedingungen
            if($db->exists('conditions')) {

                $data['conditions'] = array();
                foreach($db->hGetAll('conditions') as $id => $value) {

                    $data['conditions:'. $id] = $value;
                }
            }

            //Ereignisse
            if($db->exists('events')) {

                $data['events'] = array();
                foreach($db->hGetAll('events') as $id => $value) {

                    $data['events:'. $id] = $value;
                }
            }

            //Raum uebersicht
            if($db->exists('roomView')) {

                $data['roomView'] = array();
                foreach($db->hGetAll('roomView') as $id => $value) {

                    $data['roomView:'. $id] = $value;
                }
            }

            //Raeume
            if($db->exists('rooms')) {

                $data['rooms'] = array();
                foreach($db->hGetAll('rooms') as $id => $value) {

                    $data['rooms:'. $id] = $value;
                }
            }

            //Sensorpunkte
            if($db->exists('sensors:sensorPoints')) {

                $data['sensors:sensorPoints'] = array();
                foreach($db->hGetAll('sensors:sensorPoints') as $id => $value) {

                    $data['sensors:sensorPoints:'. $id] = $value;
                }
            }

            //Sensoren
            if($db->exists('sensors:sensors')) {

                $data['sensors:sensors'] = array();
                foreach($db->hGetAll('sensors:sensors') as $id => $value) {

                    $data['sensors:sensors.'. $id] = $value;
                }
            }

            //Sensordaten
            if($db->exists('sensors:sensorData')) {

                $data['sensors:sensorData'] = array();
                for($i = 0; $i <= 999; $i++) {

                    if($db->exists('sensors:sensorData:'. $i)) {

                        foreach($db->hGetAll('sensors:sensorData:'. $i) as $id => $value) {


                            $data['sensors:sensorData:'. $i .'.'. $id] = $value;
                        }
                    }
                }
            }

            //Schaltserver
            if($db->exists('switchServers')) {

                $data['switchServers'] = array();
                foreach($db->hGetAll('switchServers') as $id => $value) {

                    $data['switchServers:'. $id] = $value;
                }
            }

            //schaltbare Elemente
            if($db->exists('switchables')) {

                $data['switchables'] = array();
                foreach($db->hGetAll('switchables') as $id => $value) {

                    $data['switchables:'. $id] = $value;
                }
            }

            //Schaltpunkte
            if($db->exists('switchpoints')) {

                $data['switchpoints'] = array();
                foreach($db->hGetAll('switchpoints') as $id => $value) {

                    $data['switchpoints:'. $id] = $value;
                }
            }

            //Benutzer zu Hause
            if($db->exists('usersrathome')) {

                $data['usersrathome'] = array();
                foreach($db->hGetAll('usersrathome') as $id => $value) {

                    $data['usersrathome:'. $id] = $value;
                }
            }

            $zip->addFromString('database_dump.json', json_encode($data));

            //XML Dateien ins Archiv kopieren
            $xmlFiles = XmlFileManager::getInstance()->listKnownXmlFiles();
            foreach($xmlFiles as $xmlFile) {

                $info = pathinfo($xmlFile['file']);
                $zip->addFile($xmlFile['file'], $info['basename']);
            }

            //Archiv schliesen
            $zip->close();
            return true;
        }
        return false;


        /*
        if(file_exists($this->backupPath .'shc_' . DateTime::now()->format('Y_m_d') . '.zip')) {

            for($i = 1; $i <= 20; $i++) {

                if(file_exists($this->backupPath .'shc_' . DateTime::now()->format('Y_m_d') . '_' . $i . '.zip')) {

                    continue;
                } else {

                    $filename = $this->backupPath .'shc_' . DateTime::now()->format('Y_m_d') . '_' . $i . '.zip';
                    break;
                }
            }

            if($filename == '') {

                $filename = $this->backupPath .'shc_' . DateTime::now()->format('Y_m_d') . '_21.zip';
            }
        } else {

            $filename = $this->backupPath .'shc_' . DateTime::now()->format('Y_m_d') . '.zip';
        }

        $zip = new \ZipArchive();
        if ($zip->open($filename, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) === true) {

            /* Dateibackup
            if($this->addDir($zip, PATH_BASE, '', $ignoreHiddenFiles)) {

                $zip->close();
                return true;
            }

            $xmlFiles = XmlFileManager::getInstance()->listKnownXmlFiles();
            foreach($xmlFiles as $xmlFile) {

                $dir = dirname($xmlFile['file']);
                $filename = str_replace($dir, '', $xmlFile['file']);

                if(file_exists($xmlFile['file'])) {

                    $archivePath = str_replace(PATH_BASE, '', $dir);
                    $zip->addEmptyDir($archivePath);
                    $zip->addFile($xmlFile['file'], $archivePath . $filename);
                }
            }
            $zip->close();
            return true;
            //var_dump($xmlFiles);
            //$zip->addEmptyDir('rwf/data/storage');
            //$zip->addFile();
        }
        return false;
*/
    }

    /**
     * packt einen Ordner in ein Zip Archiv
     *
     * @param  ZipArchive $zip               geoeffnetes Zip Archiv
     * @param  String     $path              Pfad im Dateisystem
     * @param  String     $innerArchivePath  Pfad im Archiv
     * @param  Boolean    $ignoreHiddenFiles versteckte Dateien ignorieren
     * @return Boolean
     */
    protected function addDir($zip, $path, $innerArchivePath = '', $ignoreHiddenFiles = false) {
;
        //Ordner existiert nicht
        if (!@is_dir($path) || FileUtil::addTrailigSlash($innerArchivePath) == FileUtil::addTrailigSlash(str_replace(PATH_BASE, '', $this->backupPath))) {

            return false;
        }

        //Ordner Durchlaufen
        $dir = opendir($path);
        $path = FileUtil::addTrailigSlash($path);
        while ($file = readdir($dir)) {

            //Element
            $fileSystemElement = $path . $file;
            if ($innerArchivePath != '') {

                $innerArchiveElement = FileUtil::addTrailigSlash($innerArchivePath) . $file;
            } else {

                $innerArchiveElement = $file;
            }

            //nutzlose Elemente ueberspringen
            if ($file == '.' || $file == '..') {

                continue;
            }

            //Versteckte Elemente ueberspringen
            if ($ignoreHiddenFiles == true && preg_match('#^\.#', $file)) {

                continue;
            }

            //Datei
            if (@is_file($fileSystemElement)) {

                $zip->addFile($fileSystemElement, $innerArchiveElement);
                continue;
            }

            //Ordner
            if (@is_dir($fileSystemElement)) {

                $zip->addEmptyDir($innerArchiveElement);
                $this->addDir($zip, $fileSystemElement, $innerArchiveElement, $ignoreHiddenFiles);
                continue;
            }
        }
        return true;
    }

    /**
     * geschuetzt wegen Singleton
     */
    private function __clone() {

    }

    /**
     * gibt den Backup Editor zurueck
     *
     * @return \SHC\Backup\BackupEditor
     */
    public static function getInstance() {

        if (self::$instance === null) {

            self::$instance = new BackupEditor();
        }
        return self::$instance;
    }
}