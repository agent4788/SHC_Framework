<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Request\Commands\ActionCommand;
use RWF\Session\Login;

/**
 * Logout
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class LogoutAction extends ActionCommand {

    /**
     * Ziel nach dem ausfuehren
     * 
     * @var String
     */
    protected $location = 'index.php?app=shc&page=index&m';

    /**
     * Aktion ausfuehren
     */
    public function executeAction() {
        
        Login::logoutUser();
    }
    
}
