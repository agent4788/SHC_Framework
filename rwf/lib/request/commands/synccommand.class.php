<?php

namespace RWF\Request\Commands;

//Imports
use RWF\Request\AbstractCommand;

/**
 * Syncronisations Anfrage (Spezialform eine AJAX Anfrage)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
abstract class SyncCommand extends AbstractCommand {

    /**
     * Antwortdaten
     * 
     * @var Array
     */
    protected $data = array();

    /**
     * fuehrt das Kommando aus
     */
    protected function executeCommand() {

        $this->processData();
        $this->writeData();
    }

    /**
     * Daten verarbeiten
     */
    public abstract function processData();

    /**
     * schreibt die Daten in das Antwortobjekt
     */
    public function writeData() {

        if (is_array($this->data)) {

            //als JSON senden
            JSON::sendJSON($this->data, $this->response);
        } else {

            //als HTML oder Text Fragment senden
            $this->response->write($this->data);
        }
    }

}
