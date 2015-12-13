<?php

namespace SHC\Sheduler\Tasks;

//Imports
use RWF\Core\RWF;
use SHC\Sheduler\AbstractTask;

/**
 * wenn aktiviert kann eine LED mit 0,5Hz Binktakt anzeigen ob der Sheduler läuft
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class BlinkTask extends AbstractTask {

    /**
     * Prioriteat
     *
     * @var Integer
     */
    protected $priority = 1;

    /**
     * Wartezeit zwischen 2 durchläufen
     *
     * @var String
     */
    protected $interval = 'PT1S';

    /**
     * GPIO Pin
     *
     * @var Integer
     */
    protected $pin = -1;

    /**
     * wiringpi GPIO Befehl
     *
     * @var String
     */
    protected $gpioPath = '';

    /**
     * Status
     *
     * @var Integer
     */
    protected $state = 0;

    /**
     * gibt an ob die Anwendung initialisiert ist
     *
     * @var Boolean
     */
    protected $init = false;

    /**
     * fuehrt die Aufgabe aus
     * falls ein Intervall angegeben ist wird automatisch die Ausfuerung in den vogegebenen Zeitabstaenden verzoegert
     */
    public function executeTask() {

        if($this->init === false) {

            //GPIO Vorbereiten
            $this->pin = RWF::getSetting('shc.shedulerDaemon.blinkPin');
            $this->gpioPath = '/usr/local/bin/gpio';

            if($this->pin >= 0) {

                @shell_exec($this->gpioPath . ' mode ' . escapeshellarg($this->pin) . ' out');
            }
            $this->init = true;
        }

        //wenn Pin >= 0 Blinken mit 0,5Hz
        if($this->pin >= 0) {

            if($this->state === 0) {

                @shell_exec($this->gpioPath . ' write ' . escapeshellarg($this->pin) . ' 1');
                $this->state = 1;
            } else {

                @shell_exec($this->gpioPath . ' write ' . escapeshellarg($this->pin) . ' 0');
                $this->state = 0;
            }
        }
    }

}