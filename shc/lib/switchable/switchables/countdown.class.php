<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;
use RWF\Date\DateTime;
use SHC\Switchable\Switchable;
use SHC\Switchable\SwitchableEditor;

/**
 * Countdown (schaltet nach einer vorgegebenen Zeit automatisch zuruck in den jeweils anderen Schaltzustand)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Countdown extends AbstractSwitchable {

    /**
     * liste mit den zu schaltenden Elementen
     * 
     * @var Array
     */
    protected $switchables = array();

    /**
     * Zeitintervall
     * 
     * @var int
     */
    protected $intervall = null;

    /**
     * Ausschaltzeit
     * 
     * @var \RWF\Date\DateTime 
     */
    protected $switchOffTime = null;

    /**
     * registriet ein Schaltbares Objekt
     * 
     * @param  \SHC\Switchable\Switchable $switchable Objekt
     * @param  int                        $command    Befehl
     * @return \SHC\Switchable\Switchables\Countdown
     * @throws \Exception
     */
    public function addSwitchable(Switchable $switchable, $command = self::STATE_ON) {

        if ($switchable instanceof Countdown) {

            throw new \Exception('Ein Countdown kann nicht bei einem Countdown registriert werden', 1504);
        }

        $this->switchables[] = array('object' => $switchable, 'command' => $command);
        return $this;
    }

    /**
     * entfernt ein Schaltbares Objekt
     * 
     * @param  \SHC\Switchable\Switchable $switchable
     * @return \SHC\Switchable\Switchables\Countdown
     */
    public function removeSwitchable(Switchable $switchable) {

        foreach ($this->switchables as $index => $switchableObject) {

            if ($switchableObject['object'] == $switchable) {

                unset($this->switchables[$index]);
            }
        }
        return $this;
    }

    /**
     * entfernt alle Schaltbaren Objekte
     * 
     * @return \SHC\Switchable\Switchables\Countdown
     */
    public function removeAllSwitchables() {

        $this->switchables = array();
        return $this;
    }

    /**
     * gibt ein Array mit allen schaltbaren Elementen zurueck
     *
     * @return Array
     */
    public function listSwitchables() {

        return $this->switchables;
    }

    /**
     * setzt den Zeitintervall fuer das ausschalten
     * 
     * @param  int $interval Zeitintervall
     * @return \SHC\Switchable\Switchables\Countdown
     */
    public function setInterval($interval) {

        $this->intervall = $interval;
        return $this;
    }

    /**
     * gibt den Zeitintervall fuer das Ausschalten (in Sekunden)
     * 
     * @return Integer
     */
    public function getInterval() {

        return $this->intervall;
    }

    /**
     * setzt die Ausschalt Zeit
     * 
     * @param  \RWF\Date\DateTime $time
     * @return \SHC\Switchable\Switchables\Countdown
     */
    public function setSwitchOffTime(DateTime $time) {
        
        $this->switchOffTime = $time;
        return $this;
    }
    
    /**
     * gibt das Zeitobjekt der Ausschaltzeit zurueck
     * 
     * @return \RWF\Date\DateTime
     */
    public function getSwitchOffTime() {

        return $this->switchOffTime;
    }

    /**
     * schaltet das Objekt ein
     * 
     * @return Boolean
     */
    public function switchOn() {

        foreach ($this->switchables as $switchable) {

            /* @var $object \SHC\Switchable\Switchable */
            $object = $switchable['object'];
            $command = $switchable['command'];

            if ($command == self::STATE_ON) {

                $object->switchOn();
            } else {

                $object->switchOff();
            }
        }

        $this->switchOffTime = DateTime::now()->add(new \DateInterval('PT'. $this->intervall .'S'));
        $this->state = self::STATE_ON;
        $this->stateModified = true;
        SwitchableEditor::getInstance()->editCountdownSwitchOffTime($this->getId(), $this->switchOffTime);
    }

    /**
     * schaltet das Objekt aus
     * Das ausschalten wird vom CountdownSheduler durchgefuehrt
     * 
     * @return Boolean
     */
    public function switchOff() {

        foreach ($this->switchables as $switchable) {

            /* @var $object \SHC\Switchable\Switchable */
            $object = $switchable['object'];
            $command = $switchable['command'];

            if ($command == self::STATE_ON) {

                $object->switchOff();
            } else {

                $object->switchOn();
            }
        }
        $this->state = self::STATE_OFF;
        $this->stateModified = true;
        $this->switchOffTime = new DateTime('2000-01-01 00:00:00');
        SwitchableEditor::getInstance()->editCountdownSwitchOffTime($this->getId(), $this->switchOffTime);
    }

}
