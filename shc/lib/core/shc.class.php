<?php

namespace SHC\Core;

//Imports
use RWF\Core\RWF;

/**
 * Kernklasse (initialisiert das SHC)
 * 
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class SHC extends RWF {
    
    public function __construct() {
        
        //Basisklasse initalisieren
        parent::__construct();
        
        //Template Ordner anmelden
        self::$template->addTemplateDir(PATH_SHC .'data/templates');
    }
}
