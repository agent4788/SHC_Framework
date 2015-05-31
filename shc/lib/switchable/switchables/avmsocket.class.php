<?php

namespace SHC\Switchable\Switchables;

//Imports
use RWF\AVM\FritzBoxFactory;
use SHC\Switchable\AbstractSwitchable;

/**
 * AVM DECT oder DLAN Steckdose
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class AvmSocket extends AbstractSwitchable {

    /**
     * Identifizierung
     *
     * @var String
     */
    protected $ain = '';

    /**
     * @param String $aim Identifizierung
     */
    public function __construct($ain = '') {

        $this->ain = $ain;
    }

    /**
     * setzt die Identifizierung
     *
     * @param  String $ain Identifizierung
     * @return \SHC\Switchable\Switchables\AvmSocket
     */
    public function setAin($ain) {

        $this->ain = $ain;
        return $this;
    }

    /**
     * gibt die Identifizierung zurueck
     *
     * @return String
     */
    public function getAin() {

        return $this->ain;
    }

    /**
     * schaltet das Objekt ein
     *
     * @return Boolean
     */
    public function switchOn() {

        $fb = FritzBoxFactory::getFritzBox();
        $fbSmartHome = $fb->getSmartHome();
        $fbSmartHome->switchOn($this->ain);
        $this->state = self::STATE_ON;
        $this->stateModified = true;
    }

    /**
     * schaltet das Objekt aus
     *
     * @return Boolean
     */
    public function switchOff() {

        $fb = FritzBoxFactory::getFritzBox();
        $fbSmartHome = $fb->getSmartHome();
        $fbSmartHome->switchOff($this->ain);
        $this->state = self::STATE_OFF;
        $this->stateModified = true;
    }

}