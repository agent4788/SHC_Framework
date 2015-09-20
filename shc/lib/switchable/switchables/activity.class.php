<?php

namespace SHC\Switchable\Switchables;

//Imports
use SHC\Switchable\AbstractSwitchable;
use SHC\Switchable\Switchable;

/**
 * Aktivitaet (ermoeglicht Gruppierung von Schaltbaren Elementen)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class Activity extends AbstractSwitchable {

    /**
     * liste mit den zu schaltenden Elementen
     * 
     * @var Array
     */
    protected $switchables = array();

    /**
     * registriet ein Schaltbares Objekt
     * 
     * @param  \SHC\Switchable\Switchable $switchable Objekt
     * @param  int                        $command    Befehl
     * @return \SHC\Switchable\Switchables\Countdown
     * @throws \Exception
     */
    public function addSwitchable(Switchable $switchable, $command = self::STATE_ON) {

        if ($switchable instanceof Activity) {

            throw new \Exception('Eine Aktivität kann nicht bei einer Aktivität registriert werden', 1505);
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
    }

    /**
     * schaltet das Objekt aus
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
        $this->stateModified = true;
    }

    /**
     * gibt den aktuellen geschaltenen Zustand zurueck
     * 
     * @return Integer
     */
    public function getState() {

        foreach ($this->switchables as $switchable) {

            /* @var $object \SHC\Switchable\Switchable */
            $object = $switchable['object'];
            $command = $switchable['command'];
            $state = $object->getState();
            if (($command == self::STATE_ON && $state == self::STATE_OFF) || ($command == self::STATE_OFF && $state == self::STATE_ON)) {

                return self::STATE_OFF;
            }
        }
        return self::STATE_ON;
    }

}
