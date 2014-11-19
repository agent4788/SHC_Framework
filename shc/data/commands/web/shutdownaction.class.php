<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\ActionCommand;

/**
 * Herunterfahren
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class ShutdownAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.ucp.shutdown';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=shc';

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        exec('sudo halt');
    }
}