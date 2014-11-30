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
     * maximale Ausfuehrungszeit
     *
     * @var Integer
     */
    protected $maxExecutionTime = 30;

    /**
     * fuehrt das Kommando aus
     */
    protected function executeCommand() {

        //max Execution Time setzen
        if(function_exists('set_time_limit')) {

            set_time_limit($this->maxExecutionTime);
        }
        //direkte Ausgabe an den Browser
        ob_implicit_flush();

        //Daten verarbeiten
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

        $this->response->flush();;
    }

}
