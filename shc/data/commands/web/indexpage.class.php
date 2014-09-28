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
     * Template
     * 
     * @var String
     */
    protected $template = '';

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $sensors = \SHC\Sensor\SensorPointEditor::getInstance()->listSensors();
        var_dump($sensors[1]->getTemperature());
        var_dump($sensors[1]->getHumidity());
        var_dump($sensors[2]->getValue());
    }
}
