<?php

namespace RWF\Request;

//Imports
use RWF\Util\FileUtil;

/**
 * Representiert eine Hochgeladene Datei
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class File {

    /**
     * Upload Feld Name
     * 
     * @var String
     */
    protected $fieldName = '';

    /**
     * Content Type
     * 
     * @var String
     */
    protected $mimeType = '';

    /**
     * Dateigroeße
     * 
     * @var Integer
     */
    protected $size = 0;

    /**
     * Dateiname
     * 
     * @var String
     */
    protected $file = '';

    /**
     * Temporaerer Name
     * 
     * @var String
     */
    protected $tmpFile = '';

    /**
     * Fehlercode
     * 
     * @var Integer
     */
    protected $error = 0;

    /**
     * Upload OK
     * 
     * @var Integer
     */
    const UPLOAD_ERR_OK = UPLOAD_ERR_OK;

    /**
     * Upload groeßer als in upload_max_filesize in der php.ini festgelegt
     * 
     * @var Integer
     */
    const UPLOAD_ERR_INI_SIZE = UPLOAD_ERR_INI_SIZE;

    /**
     * Upload groeßer als MAX_FILE_SIZE im HTML Formular festgelegt
     * 
     * @var Integer
     */
    const UPLOAD_ERR_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;

    /**
     * Upload Datei nur Teilweiße hochgeladen
     * 
     * @var Integer
     */
    const UPLOAD_ERR_PARTIAL = UPLOAD_ERR_PARTIAL;

    /**
     * Upload keine Datei Hochgeladen
     * 
     * @var Integer
     */
    const UPLOAD_ERR_NO_FILE = UPLOAD_ERR_NO_FILE;

    /**
     * Upload kein Temp Ordner festgelegt
     * 
     * @var Integer
     */
    const UPLOAD_ERR_NO_TMP_DIR = UPLOAD_ERR_NO_TMP_DIR;

    /**
     * Upload Datei konnte nicht in den Temp Ordner geschrieben werden
     * 
     * @var Integer
     */
    const UPLOAD_ERR_CANT_WRITE = UPLOAD_ERR_CANT_WRITE;

    /**
     * Upload Datei Erweiterung fehler
     * 
     * @var Integer
     */
    const UPLOAD_ERR_EXTENSION = UPLOAD_ERR_EXTENSION;

    /**
     * Upload Datei ist nicht mit HTTP POST Hochgeladen
     * 
     * @var Integer
     */
    const UPLOAD_ERR_NOT_HTTP_UPLOADED_FILE = 128;

    /**
     * @param String $name Feldname
     * @param Array  $file Dateidaten
     */
    public function __construct($name, array $file) {

        $this->fieldName = $name;
        if ($file['error'] == 0) {

            //Check ob mit HTTP Hochgeladen
            if (!is_uploaded_file($this->tmpFile)) {

                $this->file = $file['name'];
                $this->error = self::UPLOAD_ERR_NOT_HTTP_UPLOADED_FILE;
            }

            $this->mimeType = $file['type'];
            $this->size = $file['size'];
            $this->file = $file['name'];
            $this->tmpFile = $file['tmp_name'];
            $this->error = $file['error'];
        } else {

            $this->file = $file['name'];
            $this->error = $file['error'];
        }
    }

    /**
     * gibt die Dateigroeße in Bytes zurueck
     * 
     * @return Integer Bytes
     */
    public function getSize() {

        return FileUtil::getFileSize($this->tmpFile);
    }

    /**
     * gibt den Dateinamen der Hochgeladenen Datei zurueck
     * 
     * @return String Dateiname
     */
    public function getFileName() {

        return $this->file;
    }

    /**
     * gibt den Temporaeren Dateinamen der Hochgeladenen Datei zurueck
     * 
     * @return String Dateiname
     */
    public function getTempfileName() {

        return $this->tmpFile;
    }

    /**
     * gibt den Namen das Upload Feldes zurueck
     * 
     * @return String Feldname
     */
    public function getFieldName() {

        return $this->fieldName;
    }

    /**
     * gibt den Fehlercode des Uploades zurueck
     * 
     * @return Integer Fehlercode
     */
    public function getErrorCode() {

        return $this->error;
    }

    /**
     * verschiebt die Hochgeladene Datei in den Zielordner
     * 
     * @param  String  $destination Zielordner
     * @return Boolean
     */
    public function move($destination) {

        if (move_uploaded_file($this->tmpFile, $destination)) {

            return true;
        }

        return false;
    }

}
