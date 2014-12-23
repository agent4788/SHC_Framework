<?php

namespace PCC\Command\Web;

//Imports
use RWF\Request\Commands\ActionCommand;

/**
 * Neustart
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class RebootAction extends ActionCommand {

    /**
     * benoetigte Berechtigung
     *
     * @var String
     */
    protected $requiredPremission = 'shc.ucp.reboot';

    /**
     * Ziel nach dem ausfuehren
     *
     * @var String
     */
    protected $location = 'index.php?app=pcc';

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {

        exec('sudo reboot -n');
    }
}