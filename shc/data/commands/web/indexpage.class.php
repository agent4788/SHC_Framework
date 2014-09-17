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
        
        var_dump((new \SHC\Condition\Conditions\SunriseSunsetCondition())->isSatisfies());
        var_dump((new \SHC\Condition\Conditions\SunsetSunriseCondition())->isSatisfies());
        var_dump((new \SHC\Condition\Conditions\DayOfWeekCondition())->setData(array('start' => 'thu', 'end' => 'sat'))->isSatisfies());
        var_dump((new \SHC\Condition\Conditions\TimeOfDayCondition())->setData(array('start' => '05:00', 'end' => '19:00'))->isSatisfies());
    }
}
