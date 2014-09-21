<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;
use RWF\Date\DateTime;
use SHC\Switchable\Switchable;

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
     * @var type 
     */
    protected $switchables = array();

    /**
     * Zeitintervall
     * 
     * @var \DateIntervall 
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
     * @param  Integre                    $command    Befehl
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
     * setzt den Zeitintervall fuer das ausschalten
     * 
     * @param  \DateInterval $intervall Zeitintervall
     * @return \SHC\Switchable\Switchables\Countdown
     */
    public function setIntervall(\DateInterval $intervall) {

        $this->intervall = $intervall;
        return $this;
    }

    /**
     * gibt den Zeitintervall fuer das Ausschalten 
     * 
     * @return \DateIntervall
     */
    public function getIntervall() {

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

        $this->switchOffTime = DateTime::now()->add($this->intervall);
        //Update Ausschalzzeit im Schaltbare ELemente Editor
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
    }

}
