<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;

/**
 * Script
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Script extends AbstractSwitchable {

    /**
     * Kommandozeilenbefehl zum einschalten
     *
     * @var String
     */
    protected $onCommand = '';

    /**
     * Kommandozeilenbefehl zum ausschalten
     *
     * @var String
     */
    protected $offCommand = '';

    /**
     * @param String $onCommand
     * @param String $offCommand
     */
    public function __construct($onCommand = '', $offCommand = '') {

        $this->onCommand = $onCommand;
        $this->offCommand = $offCommand;
    }

    /**
     * setzt das Kommando zum einschalten
     *
     * @param  String $onCommand
     * @return \SHC\Switchable\Switchables\Script
     */
    public function setOnCommand($onCommand) {

        $this->onCommand = $onCommand;
        return $this;
    }

    /**
     * gibt das Kommando zum einschalten zurueck
     *
     * @return String
     */
    public function getOnCommand() {

        return $this->onCommand;
    }

    /**
     * setzt das Kommando zum ausschalten
     *
     * @param  String $offCommand
     * @return \SHC\Switchable\Switchables\Script
     */
    public function setOffCommand($offCommand) {

        $this->offCommand = $offCommand;
        return $this;
    }

    /**
     * gibt das Kommando zum Ausschalten zurueck
     *
     * @return String
     */
    public function getOffCommand() {

        return $this->offCommand;
    }

    /**
     * schaltet das Objekt ein
     *
     * @return Boolean
     */
    public function switchOn() {

        @exec($this->onCommand);

        //Status nur speichern wenn on und off Command gesetzt
        if($this->onCommand != '' && $this->offCommand != '') {

            $this->state = self::STATE_ON;
            $this->stateModified = true;
        }
    }

    /**
     * schaltet das Objekt aus
     *
     * @return Boolean
     */
    public function switchOff() {

        @exec($this->offCommand);

        //Status nur speichern wenn on und off Command gesetzt
        if($this->onCommand != '' && $this->offCommand != '') {

            $this->state = self::STATE_OFF;
            $this->stateModified = true;
        }
    }

}