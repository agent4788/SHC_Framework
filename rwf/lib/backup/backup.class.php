<?php

namespace RWF\Backup;

//Imports
use RWF\Util\FileUtil;

/**
 * Backup
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Backup {

    /**
     * Hash
     *
     * @var String
     */
    protected $hash = '';

    /**
     * Pfad der Backups
     *
     * @var String
     */
    protected $packupPath = '';

    /**
     * Dateiname
     *
     * @var String
     */
    protected $file = '';

    /**
     * @param String $hash       Pruefsumme
     * @param String $packupPath Pfad
     * @param String $file       Dateiname
     */
    public function __construct($hash, $packupPath, $file){

        $this->hash = $hash;
        $this->backupPath = FileUtil::addTrailigSlash($packupPath);
        $this->file = $file;
    }

    /**
     * gibt den Hash der Datei zurueck
     *
     * @return String
     */
    public function getHash() {

        return $this->hash;
    }

    /**
     * gibt den Pfad der Backups zurueck
     *
     * @return String
     */
    public function getPath() {

        return $this->backupPath;
    }

    /**
     * gibt den Dateinamen zurueck
     *
     * @return String
     */
    public function getFileName() {

        return $this->file;
    }

    /**
     * gibt die Dateigroeße zurueck
     *
     * @return Integer
     */
    public function getSize() {

        return FileUtil::getFileSize($this->backupPath . $this->file);
    }
}