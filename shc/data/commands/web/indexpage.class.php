<?php

namespace SHC\Command\Web;

//Imports
use RWF\Request\Commands\PageCommand;

/**
 * Startseite
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class IndexPage extends PageCommand {
    
    /**
     * Daten verarbeiten
     */
    public function processData() {

        $this->response->writeLn('test');
    }
}
